<?php

     /**
     * Removes the underscores from any given string
     * @param string $string represents the string to mutate.
     */
    function removeUnderscores($string){
        $replace = str_replace('_', ' ', $string);
        return $replace;
    }

    /**
     * return the states above a spesific states
     * @param string $state_name a spesific state to be checked
     * @return array $array array of the states higher then the spesific state in the hierarchy
     */
    function statesNotIncluded(string $state_name){
        $res = array();
        if ($state_name == 'new') {
            $res = ['open', 'skis available', 'ready to be shipped', 'shipped'];
        } else if ($state_name == 'open') {
            $res = ['skis available', 'ready to be shipped', 'shipped'];
        } else if ($state_name == 'skis_available') {
            $res = ['ready to be shipped', 'shipped'];
        } else if ($state_name == 'ready_to_be_shipped') {
            $res = ['shipped'];
        } else if ($state_name == 'shipped') {
            $res = [];
        }
        return $res;
    }

    /**
     * converts state to an internal value that represent it's value in a hierarchy
     * @param string $state_name the state name to be converted
     * @return int the value related to the state name
     */
    function stateToValue(string $state_name){
        if ($state_name == 'new') {
            return 0;
        } else if ($state_name == 'open') {
            return 1;
        } else if ($state_name == 'skis available') {
            return 2;
        } else if ($state_name == 'ready to be shipped') {
            return 3;
        } else if ($state_name == 'shipped') { // will in theory never get this
            return 4;
        }
        return 0;
    }

    /**
     * converts the internal state value to the state name
     * @param int $value the state value to be converted
     * @return string the state name related to the state value
     */
    function valueToState(int $value){
        if ($value == 0) {
            return 'new';
        } else if ($value == 1) {
            return 'open';
        } else if ($value == 2) {
            return 'skis available';
        } else if ($value == 3) {
            return 'ready to be shipped';
        } else if ($value == 4) { // will in theory never get this
            return 'shipped';
        }
        return 'new';
    }