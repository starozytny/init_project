import React, { Component } from "react";

import axios from "axios";
import "leaflet/dist/leaflet.css";
import L from "leaflet/dist/leaflet";
import "leaflet-ajax/dist/leaflet.ajax.min";

import Map from "@dashboardComponents/functions/map";

export class StyleguideMaps extends Component{
    constructor(props) {
        super(props);

        this.state = {
            choices: []
        }

        this.handleChoice = this.handleChoice.bind(this);
    }

    handleChoice = (id) => {
        const { choices } = this.state;
        let newChoices = [];

        let isIn = false;
        choices.forEach(el => {
            if(el === id){
                isIn = true;
            }
            newChoices.push(el);
        })

        if(isIn){
            newChoices = choices.filter(el => el !== id);
        }else{
            newChoices.push(id);
        }

        this.setState({choices: newChoices})
    }

    componentDidMount = () => {

        let mymap = Map.createMap(51.505, -0.09);

        let circle = L.circle([51.508, -0.11], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 500
        }).addTo(mymap);
        let polygon = L.polygon([
            [51.509, -0.08],
            [51.503, -0.06],
            [51.51, -0.047]
        ]).addTo(mymap);

        let geojsonFeature = {
            "type": "Feature",
            "properties": {
                "name": "Coors Field",
                "amenity": "Baseball Stadium",
                "popupContent": "This is where the Rockies play!"
            },
            "geometry": {
                "type": "Point",
                "coordinates":[-0.09, 51.495]
            }
        };
        L.geoJSON(geojsonFeature, {pointToLayer: function (feature, latlng) {
                return L.marker(latlng, {icon: Map.getOriginalLeafletIcon()});
            },}).addTo(mymap);

        axios.get('../../maps/data/ecoles.json')
            .then(function (response){
                response.data.forEach(el => {
                    let marker = L.marker([el.Latitude, el.Longitude], {icon: Map.getLeafletMarkerIcon("book")}).addTo(mymap);
                    marker.bindPopup("<b>"+el.name+"</b> <br/>" + el.Categorie).openPopup();
                })
            })
        ;

    }

    render () {
        const { choices } = this.state;
        const divStyle = {
            height: '50vh'
        };

        let choiceItems = [
            {id: 0, label: 'Ecoles maternelles', icon: 'book'}
        ]

        let mapChoices = [];
        choiceItems.forEach(el => {
            let active = "";
            if(choices.includes(el.id)){
                active = " active"
            }
            mapChoices.push(<div className={"maps-choice" + active} onClick={() => this.handleChoice(el.id)} key={el.id}>
                <div className="map-label">
                    <div className="icon"><span className={"icon-" + el.icon} /></div>
                    <div>{el.label}</div>
                </div>
            </div>)
        })

        let choicesActive = "";
        choices.forEach(el => {
            choicesActive += " maps-choices-" + el
        })

        return (
            <section>
                <h2>OpenstreetMaps - Leaflet</h2>
                <div className={"maps-items" + choicesActive}>
                    <div id="mapid" style={divStyle} />
                    <div className="maps-choices">
                        {mapChoices}
                    </div>
                </div>
            </section>
        )
    }

}