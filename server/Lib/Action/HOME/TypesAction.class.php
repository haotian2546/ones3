<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TypesAction
 *
 * @author nemo
 */
class TypesAction extends CommonAction {
    
    protected function _filter(&$map) {
        if($_GET["type"]) {
            $map["type"] = $_GET["type"];
        }
    }

    protected function _order(&$order) {
        if($_GET["type"]) {
            $order = "listorder DESC";
        }
    }

    
}