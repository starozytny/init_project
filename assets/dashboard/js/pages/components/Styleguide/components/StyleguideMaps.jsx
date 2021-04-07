import React, { Component } from "react";

import axios from "axios";
import "leaflet/dist/leaflet.css";
import L from "leaflet/dist/leaflet";
import "leaflet-ajax/dist/leaflet.ajax.min";

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
        let mymap = L.map('mapid').setView([51.505, -0.09], 13);
        L.tileLayer('https://b.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Tuiles de fond <a href="https://www.openstreetmap.org/copyright">OSM.org</a>',
            maxZoom: 18,
        }).addTo(mymap);

        // let leafletIcon = L.icon({
        //     iconUrl: '../../maps/images/marker-icon.png',
        //     shadowUrl: '../../maps/images/marker-shadow.png',
        //     iconSize:     [25, 41], // size of the icon
        //     shadowSize:   [41, 41], // size of the shadow
        //     iconAnchor:   [9, 40], // point of the icon which will correspond to marker's location 38 95
        //     shadowAnchor: [9, 40],  // the same for the shadow 50 61
        //     popupAnchor:  [4, -35] // point from which the popup should open relative to the iconAnchor
        // })
        let leafletIcon = L.divIcon({
            className: 'map-marker-icon map-marker-icon-0',
            html: "<div style='background-color:#4838cc;' class='marker-pin'></div><span class='icon-book'>",
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor:  [0, -35]
        })

        let marker = L.marker([51.5, -0.09], {icon: leafletIcon}).addTo(mymap);
        marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();
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
                return L.marker(latlng, {icon: leafletIcon});
            },}).addTo(mymap);

        axios.get('../../maps/data/ecoles.json')
            .then(function (response){
                response.data.forEach(el => {
                    let marker = L.marker([el.Latitude, el.Longitude], {icon: leafletIcon}).addTo(mymap);
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