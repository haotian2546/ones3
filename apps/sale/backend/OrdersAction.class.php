<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrdersAction
 *
 * @author 志鹏
 */
class OrdersAction extends CommonAction {
    
    protected $modelName = "Orders";
    
    protected $indexModel = "OrdersView";
    
    protected $modelDetailName = "OrdersDetail";
    
    protected $mainRowIdField = "order_id";
    
    protected $workflowAlias = "orders";
    
    protected $readModel = "OrdersView";
    
    protected $ajaxRowFields = array(
        "factory_code_all"=>"","goods_name"=>"","color_name"=>"",
        "standard_name"=>"", "per_price"=>"input","num"=>"input.",
        "store_num"=>"span.badge badge-info","price"=>"input"
    );
    
    protected $relationModel = "Stockout";
    
    protected function _filter(&$map) {

    }
    
    public function read() {
        
        if(!$_GET["includeRows"] or $_GET['workflow']) {
            return parent::read();
        }
        
        $this->readModel = "OrdersView";
        $formData = parent::read(true);

        $formData["inputTime"] = $formData["dateline"]*1000;
        
        $rowModel = D("OrdersDetailView");
        $rows = $rowModel->where("OrdersDetail.order_id=".$formData["id"])->select();

        $modelIds = array();
        $rowData = array();
        foreach($rows as $v) {
            $tmp = explode(DBC("goods.unique.separator"), $v["factory_code_all"]); //根据factory_code_all factory_code - standard - version
            $factory_code = array_shift($tmp);
            $modelIds = array_merge($modelIds, $tmp);
            
            $v["modelIds"] = $tmp;
            $v["goods_id"] = sprintf("%s_%s_%s", $factory_code, $v["goods_id"], $v["goods_category_id"]); // factory_code, id, catid
            $v["goods_id_label"] = sprintf("%s",$v["goods_name"]);
            $rowData[$v["id"]] = $v;
        }

        $formData["customer_id_label"] = $formData["customer"];
        

        $dataModel = D("DataModelDataView");
        
        $rowData = $dataModel->assignModelData($rowData, $modelIds);
        
        $formData["rows"] = reIndex($rowData);

        if($formData["tax_amount"]) {
            $formData["includeTax"] = true;
        }

        /*
         * 相关单据
         * **/
        $relateItem = array();
        $id = abs(intval($_GET["id"]));
        if(isAppLoaded("purchase")) {
            $relateItem = array_merge($relateItem, (array)D("Purchase")->toRelatedItem("Orders", $id));
        }

        if(isAppLoaded("finance")) {
            $relateItem = array_merge($relateItem, (array)D("FinanceReceivePlan")->toRelatedItem("Orders",$id));
        }

        if(isAppLoaded("produce")) {
            $relateItem = array_merge($relateItem, (array)D("ProducePlan")->toRelatedItem("Orders",$id));
        }

        $formData["relatedItems"] = $relateItem;

        $this->response($formData);
        
    }
    
    public function update() {
        $model = D("Orders");
        $theOrder = $model->find($_GET["id"]);

        if($theOrder["status"] >= 1) {
            $this->error("in_workflow");
            return false;
        }

        $data = $model->formatData($_POST);
        if(false === $model->newOrder($data)) {
            $this->error($model->getError());
            return;
        }
    }
    
    
    /**
     * 
     */
    public function insert() {

        if($_REQUEST["workflow"]) {
            return $this->doWorkflow();
        }
        
        $model = D("Orders");
        $data = $model->formatData($_POST);
        $orderId = $model->newOrder($data);
        if(false === $orderId) {
            $this->error($model->getError());
            return;
        }
        
        import("@.Workflow.Workflow");
        $workflow = new Workflow($this->workflowAlias);
        $node = $workflow->doNext($orderId, "", true);
    }
    
    //销售统计分析
    public function ACT_analytics() {
    	$quick = $_GET["_filter_timeStep"];
        switch($quick) {
            case "year":
                $starttime = strtotime((date("Y", CTS)-5)."-00-00");
                $endtime = strtotime((date("Y", CTS)+1)."-01-02");
                $step = 24*3600*365;
                $format = "Y";
                break;
            case "month":
                $starttime = strtotime(date("Y-01-01", CTS));
                $endtime = strtotime(date("Y-12-31", CTS));
                $step = 24*3600*31;
                $format = "Y-m";
                break;
            default:
                $starttime = strtotime(date("Y-m", CTS));
                $endtime = strtotime(date("Y-m-d"));
                $step = 24*3600;
                $format = "m-d";
                break;
        }
        if($_GET["_filter_start_dateline"]) {
        	$starttime = strtotime($_GET["_filter_start_dateline"]);        	
        }
        if($_GET["_filter_end_dateline"]) {
        	$endtime = strtotime($_GET["_filter_end_dateline"]);        	
        }

        switch($_GET["type"]) {
            case "top_20_customer":
            case "by_saler":
            case "by_department":
                $model = "OrdersView";
                break;
            default:
                $model = "Orders";
        }

        $map = array(
            "status" => array("EGT", 1),
            "sale_type" => getTypeIdByAlias("sale", "sale"),
            "dateline" => array("BETWEEN", array($starttime, $endtime))
        );

        $orderModel = D($model);

        $orderModel->includeWorkflowProcess = false;
        $orderSourceData = $orderModel->where($map)->select();

//        print_r($orderSourceData);exit;

//        $this->response($orderSourceData);
        switch($_GET["type"]) {
            case "top_20_customer":
                $data = $this->ForCustomerRange($orderSourceData);
                break;
            case "by_saler":
                $data = $this->ForSalerRange($orderSourceData, $starttime, $endtime, $step, $format);
                break;
            case "by_department":
                $data = $this->ForDepartmentRange($orderSourceData, $starttime, $endtime, $step, $format);
                break;
            default:
                $data = $this->ForSaleTotal($orderSourceData, $starttime, $endtime, $step, $format);
        }
        
        $this->response($data);
    }
    
    /*
     * 销售，柱状图
     * **/
    protected function ForSaleTotal($data, $start, $end, $step, $format="m-d") {
    	$dateRange = makeDateRange($start, $end, $step, $format);
    	$value = array();
    	$labels = array();
    	foreach($dateRange as $dr) {
    		$labels[$dr] = $dr;
    		$value[$dr] = 0; 
    		foreach($data as $v) {
    			$key = date($format, $v["dateline"]);
    			if($dr == $key) {
    				$value[$dr] += $v["total_amount_real"];
    			}
    		}
    	}
    	
    	return array(
    			"series" => array(lang("amount")),
    			"data" => array(reIndex($value)),
    			"labels" => reIndex($labels)
    	);
    }
    
    private function ForDepartmentRange($data, $start, $end, $step, $format="m-d") {
    	foreach($data as $k=>$v) {
    		$uids[$v["saler_id"]] = $v["saler_id"];
    	}
    
    	//涉及用户
    	$model = D("UserRelation");
    	$tmp = $model->relation(true)->where(array(
    			"id" => array("IN", $uids)
    	))->select();
    
    	foreach($tmp as $v) {
    		$users[$v["id"]] = array(
    				"id" => $v["id"],
    				"dep_id" => $v["department_id"],
    				"dep_name" => $v["Department"]["name"]
    		);
    	}
    
    	$tmp = array();
    	foreach($data as $row) {
    		$k = $users[$row["saler_id"]];
    		if(!$tmp[$k["dep_id"]]) {
    			$tmp[$k["dep_id"]]["label"] = $k["dep_name"];
    			$tmp[$k["dep_id"]]["value"] = $row["total_amount_real"];
    		} else {
    			$tmp[$k["dep_id"]]["value"] += $row["total_amount_real"];
    		}
    
    	}
    
    	$labels = array();
    	$value = array();
    	foreach($tmp as $k=>$v) {
    		$labels[] = $v["label"];
    		$value[] = $v["value"];
    	}
    	
    	return array(
    			"series" => array(lang("department")),
    			"labels" => $labels,
    			"data" => array($value)
    	);
    
    }
    
    
    private function ForSalerRange($data, $start, $end, $step, $format="m-d") {
    	$tmp = array();
    	foreach($data as $row) {
    		$k = $row["saler_id"];
    		if(!$tmp[$k]) {
    			$tmp[$k]["label"] = $row["sponsor"];
    			$tmp[$k]["value"] = $row["total_amount_real"];
    		} else {
    			$tmp[$k]["value"] += $row["total_amount_real"];
    		}
    
    	}
    	
    	$labels = array();
    	$value = array();
    	foreach($tmp as $k=>$v) {
    		$labels[] = $v["label"];
    		$value[] = $v["value"];
    	}
    
    	return array(
    			"series" => array(lang("user")),
    			"labels" => $labels,
    			"data" => array($value)
    	);
    
    }

}
