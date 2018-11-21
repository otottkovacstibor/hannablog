<?php

class WPUPG_Premium_Addon {

    public $addonPath;
    public $addonDir;
    public $addonUrl;
    public $addonName;

    public function __construct( $name )
    {
        $this->addonPath = '/addons/' . $name;
        $this->addonDir = WPUltimatePostGridPremium::get()->premiumDir . $this->addonPath;
        $this->addonUrl = WPUltimatePostGridPremium::get()->premiumUrl . $this->addonPath;
        $this->addonName = $name;
    }
}