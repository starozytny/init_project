import "leaflet/dist/leaflet.css";

import React, { Component } from "react";

import axios from "axios";
import L     from "leaflet/dist/leaflet";
import Map   from "@commonComponents/functions/map";

export class MapSingle extends Component {
    constructor(props) {
        super(props);

        this.state = {
            loadData: false,
            mymap: null,
            choiceItems: [
                {id: 0, icon: 'book',         filename: null,          label: 'Ecoles maternelles'},
                {id: 1, icon: 'book',         filename: null,          label: 'Ecoles primaires'},
                {id: 2, icon: 'book',         filename: null,          label: 'Collèges'},
                {id: 3, icon: 'book',         filename: null,          label: 'Lycées'},
                {id: 4, icon: 'home',           filename: 'services',    label: 'Services publiques'},
                {id: 5, icon: 'levels',         filename: 'sports',      label: 'Sports'},
                {id: 6, icon: 'equalizer',        filename: 'parkings',    label: 'Parking'},
                {id: 7, icon: 'share',           filename: 'velos',       label: 'Parking vélos'},
                {id: 8, icon: 'email',           filename: 'commerces',   label: 'Postes'},
                {id: 9, icon: 'heart',           filename: 'heal',        label: 'Santé'},
                {id: 10, icon: 'video',        filename: 'commerces',   label: 'Cinémas'},
                {id: 11, icon: 'star',    filename: 'commerces',   label: 'Restaurants'},
                {id: 12, icon: 'star',     filename: 'commerces',   label: 'Snack'},
                {id: 13, icon: 'filter',           filename: 'commerces',   label: 'Bars/Café'},
            ],
            choices: [],
            dataLoaded: [],
        }

        this.handleChoice = this.handleChoice.bind(this);
    }

    handleChoice = (elem) => {
        const { choices, mymap, dataLoaded } = this.state;

        let newChoices = [];
        let dataLoad = dataLoaded.filter(el => el.id !== elem.id);
        dataLoad.push(elem.id)

        let isIn = false;
        choices.forEach(el => {
            if(el.id === elem.id){
                isIn = true;
            }
            // newChoices.push(el);
        })
        if(isIn){
            newChoices = choices.filter(el => el.id !== elem.id);
        }else{
            newChoices.push(elem);
        }

        newChoices.forEach(el => {
            if(el.filename){
                getData(mymap, el, dataLoaded)
            }else{
                getDataEcoles(mymap, el, dataLoaded)
            }
        })

        this.setState({choices: newChoices, dataLoaded: dataLoad })
    }

    componentDidMount = () => {
        const { elem } = this.props;

        let mymap = Map.createMap();

        if(elem.address.lat && elem.address.lon){
            L.marker([elem.address.lat, elem.address.lon], {icon: Map.getLeafletMarkerIcon()}).addTo(mymap);
            mymap.fitBounds([[elem.address.lat, elem.address.lon]]);
        }

        this.setState({ mymap })
    }

    render () {
        const { choices, choiceItems } = this.state;

        let mapChoices = [];
        choiceItems.forEach(el => {
            let active = choices.includes(el) ? " active" : "";
            mapChoices.push(<div className={"maps-choice maps-choice-" + el.id + active} onClick={() => this.handleChoice(el)} key={el.id}>
                <div className="map-label">
                    <div className="icon"><span className={"icon-" + el.icon} /></div>
                    <div>{el.label}</div>
                </div>
            </div>)
        })

        let choicesActive = "";
        choices.forEach(el => {
            choicesActive += " maps-choices-" + el.id
        })

        return <div className={"maps-items" + choicesActive}>
            <div id="mapid" />
            <div className="maps-choices">
                {mapChoices}
            </div>
        </div>;
    }
}

function filterNoDataFrench(feature, layer) {
    if(feature.properties){
        if(feature.properties.nom){
            return true;
        }
    }
    return false;
}
function filterNoDataPoste(feature, layer) {
    return (feature.properties && feature.properties.name && feature.properties.type === "post_office")
}
function filterNoDataCinema(feature, layer) {
    return (feature.properties && feature.properties.name && feature.properties.type === "cinema")
}
function filterNoDataRestaurants(feature, layer) {
    return (feature.properties && feature.properties.name && feature.properties.type === "restaurant")
}
function filterNoDataFastFood(feature, layer) {
    return (feature.properties && feature.properties.name && feature.properties.type === "fast_food")
}
function filterNoDataBar(feature, layer) {
    return (feature.properties && feature.properties.name && (feature.properties.type === "bar" || feature.properties.type === "pub" || feature.properties.type === "cafe"))
}
function filterNoData(feature, layer) {
    if(feature.properties){
        if(feature.properties.name){
            return true;
        }else{
            if(feature.properties.type === "townhall"){
                return true;
            }
        }
    }
    return false;
}

function onEachFeatureFrench(feature, layer) {
    // does this feature have a property named popupContent?
    if (feature.properties) {
        let name = feature.properties.nom;
        layer.bindPopup("<b>" + name + "</b>");
    }
}
function onEachFeature(feature, layer) {
    // does this feature have a property named popupContent?
    if (feature.properties) {
        let name = feature.properties.name;
        if(!name){
            if(feature.properties.type === "townhall"){
                name = "Mairie de " + feature.properties.com_nom;
            }
        }
        layer.bindPopup("<b>" + name + "</b>");
    }
}

function getData(mymap, el, dataLoad) {
    if(!dataLoad.includes(el.id)) {
        axios.get('/maps/data/' + el.filename + ".geojson")
            .then(function (response) {

                let onEachF = onEachFeature;
                let onFilter = filterNoData;

                switch (el.id){
                    case 13:
                        onFilter = filterNoDataBar;
                        break;
                    case 12:
                        onFilter = filterNoDataFastFood;
                        break;
                    case 11:
                        onFilter = filterNoDataRestaurants;
                        break;
                    case 10:
                        onFilter = filterNoDataCinema;
                        break;
                    case 9:
                        // onFilter = filterNoDataPharmacy;
                        break;
                    case 8:
                        onFilter = filterNoDataPoste;
                        break;
                    case 7:
                        onEachF = function (){return "";}
                        onFilter = function (){return true;}
                        break;
                    case 6:
                        onEachF = onEachFeatureFrench;
                        onFilter = filterNoDataFrench;
                        break;
                    default:
                        break;
                }

                response = response.data
                L.geoJSON(response, {onEachFeature: onEachF, filter: onFilter, pointToLayer: function (feature, latlng) {
                        return L.marker(latlng, {icon: Map.getLeafletIcon(el)});
                    },}).addTo(mymap);
            })
        ;
    }
}

function getDataEcoles(mymap, el, dataLoad) {
    if(!dataLoad.includes(el.id)) {
        axios.get('/maps/data/ecoles.json')
            .then(function (response) {
                response = response.data
                response.forEach(elem => {
                    let go = false
                    if(elem.etat_etablissement === 1){
                        let  natureId = elem.nature_uai
                        switch (el.id){
                            case 3:
                                if(natureId === 300 || natureId === 301 || natureId === 302 || natureId === 306
                                    || natureId === 310 || natureId === 320 || natureId === 334 || natureId === 335){
                                    go = true
                                }
                                break;
                            case 2:
                                if(natureId === 340 || natureId === 390 || natureId === 302 || natureId === 306
                                    || natureId === 310 || natureId === 320 || natureId === 334 || natureId === 335){
                                    go = true
                                }
                                break;
                            case 1:
                                if(natureId === 151 || natureId === 152 || natureId === 153 || natureId === 162){
                                    go = true
                                }
                                break;
                            default:
                                if(natureId === 101 || natureId === 103 || natureId === 111){
                                    go = true
                                }
                                break;
                        }
                    }

                    if(go){
                        let marker = L.marker([elem.latitude, elem.longitude], {icon: Map.getLeafletIcon(el)}).addTo(mymap);
                        marker.bindPopup("<b>" + elem.patronyme_uai + "</b> <br/>" + elem.denomination_principale);
                    }
                })
            })
        ;
    }
}

