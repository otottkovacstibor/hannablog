<?php

class WPUPG_Addon {

    public $addonPath;
    public $addonDir;
    public $addonUrl;
    public $addonName;

    public function __construct( $name )
    {
        $this->addonPath = '/addons/' . $name;
        $this->addonDir = WPUltimatePostGrid::get()->coreDir . $this->addonPath;
        $this->addonUrl = WPUltimatePostGrid::get()->coreUrl . $this->addonPath;
        $this->addonName = $name;
    }
}