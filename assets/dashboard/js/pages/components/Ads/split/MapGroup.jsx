import "leaflet/dist/leaflet.css";

import React, { Component } from "react";

import L     from "leaflet/dist/leaflet";
import Map   from "@dashboardComponents/functions/map";
import Sanitize   from "@dashboardComponents/functions/sanitaze";

function reinitMap(self, mymap, elems, mapId, mapUrl)
{
    if(mymap){
        mymap.off();
        mymap.remove();
    }

    let newMap = Map.createMap(mapId, mapUrl, 12, 10);
    setMarkers(newMap, elems)

    self.setState({ mymap: newMap, elems: elems })
}

function setMarkers(mymap, elems)
{
    if(elems){
        let latLngs = [];
        elems.map(elem => {
            if(elem.address.lat && elem.address.lon){
                let latLon = [elem.address.lat, elem.address.lon];
                let marker = L.marker(latLon, {icon: Map.getLeafletMarkerIcon()}).addTo(mymap);
                marker.bindPopup("<b>"+elem.label+"</b><br>"+Sanitize.toFormatCurrency(elem.financial.price));
                latLngs.push(latLon)
            }
        })

        let markerBounds = L.latLngBounds(latLngs);
        mymap.fitBounds(markerBounds);
    }
}

export class MapGroup extends Component {
    constructor(props) {
        super(props);

        this.state = {
            mymap: null,
            elems: props.elems ? props.elems : [],
            mapId: props.mapId ? props.mapId : 'mapGroup',
            mapUrl: props.mapUrl ? props.mapUrl : 'https://b.tile.openstreetmap.org/{z}/{x}/{y}.png',
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { mymap, mapId, mapUrl } = this.state;

        if(prevState.elems !== this.props.elems){
            reinitMap(this, mymap, this.props.elems, mapId, mapUrl);
        }
    }

    componentDidMount = () => {
        const { elems, mapId, mapUrl } = this.state;

        reinitMap(this, null, elems, mapId, mapUrl);
    }

    render () {
        return <div className="maps-items">
            <div id="mapGroup" />
        </div>;
    }
}