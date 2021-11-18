import "leaflet/dist/leaflet.css";

import React, { Component } from "react";

import L     from "leaflet/dist/leaflet";
import Map   from "@commonComponents/functions/map";
import Sanitize   from "@commonComponents/functions/sanitaze";

function reinitMap(self, mymap, elems, mapId, mapUrl, urlAd)
{
    if(mymap){
        mymap.off();
        mymap.remove();
    }

    let newMap = Map.createMap(12, 10, mapId, mapUrl);
    setMarkers(newMap, elems, urlAd)

    self.setState({ mymap: newMap, elems: elems })
}

function setMarkers(mymap, elems, urlAd=null)
{
    if(elems){
        let latLngs = [];
        elems.map(elem => {
            if(elem.address.lat && elem.address.lon){
                let latLon = [elem.address.lat, elem.address.lon];
                let marker = L.marker(latLon, {icon: Map.getLeafletMarkerIcon()}).addTo(mymap);

                let image = elem.thumb ? "/annonces/thumbs/" + elem.agency.dirname + "/" + elem.thumb : "/immo/logos/" + elem.agency.logo;
                let href = urlAd ? "href='"+ urlAd +"'" : "";

                marker.bindPopup("<a "+ href +" target='_blank' class='popmap-item'>" +
                    "<img src='" + image + "' alt='Annonces thumbs' " + elem.label + ">" +
                    "<div class='popmap-item-infos'>" +
                        "<div class='label'>" + elem.label + "</div>" +
                        "<div class='address'>" + elem.address.zipcode + ", " + elem.address.city + "</div>" +
                        "<div class='grp-price'><span class='price'>"+Sanitize.toFormatCurrency(elem.financial.price) + "</span>" + (elem.typeAd === "Location" ? " cc/mois" : "") + "</div>" +
                    "</div>" +
                "</a>");
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
        const { urlAd } = this.props
        const { mymap, mapId, mapUrl } = this.state;

        if(prevState.elems !== this.props.elems){
            reinitMap(this, mymap, this.props.elems, mapId, mapUrl, urlAd);
        }
    }

    componentDidMount = () => {
        const { urlAd } = this.props
        const { elems, mapId, mapUrl } = this.state;

        reinitMap(this, null, elems, mapId, mapUrl, urlAd);
    }

    render () {
        return <div className="maps-items">
            <div id="mapGroup" />
        </div>;
    }
}