import '../css/app.scss';
import '@commonComponents/functions/toastrOptions';

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from 'react';
import { render } from 'react-dom';
import { Menu } from './components/Layout/Menu';
import { Notifications } from "@dashboardComponents/Notifications";
import { ChartAds, ChartBiens } from "@dashboardFolder/js/pages/components/Stats/Charts";

Routing.setRoutingData(routes);

const menu = document.getElementById("menu");
if(menu) {
    render(
        <Menu {...menu.dataset} />, menu
    )
}

const notifications = document.getElementById("notifications");
if(notifications){
    render(
        <Notifications {...notifications.dataset} />, notifications
    )
}

let chartAds = document.getElementById('chart-ads');
if(chartAds){
    render(
        <ChartAds {...chartAds.dataset} />, chartAds
    )
}

let chartBiens = document.getElementById('chart-biens');
if(chartBiens){
    render(
        <ChartBiens {...chartBiens.dataset} />, chartBiens
    )
}