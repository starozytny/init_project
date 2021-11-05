import React, { Component } from "react";

import Chart from "react-apexcharts";

export class ChartAds extends Component {
    constructor(props) {
        super(props);

        this.state = {
            series: [],
            options: {}
        }
    }

    componentDidMount = () => {
        const { donnees } = this.props;

        let data = JSON.parse(donnees);

        let locationsData = [], ventesData = [], biensData = [], legends = [];
        data.map(el => {
            biensData.push(el.totBiens);
            locationsData.push(el.totLocations);
            ventesData.push(el.totVentes);
            legends.push(el.createdAtString);
        })

        this.setState({
            series: [{
                name: 'Biens',
                data: biensData
            }, {
                name: 'Locations',
                data: locationsData
            }, {
                name: 'Ventes',
                data: ventesData
            }],
            options: {
                colors: ['#109cf1', '#cbec18', '#e25146'],
                chart: {
                    height: 200,
                    type: 'area',
                    toolbar: { show: false },
                },
                legend: { show: false },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                    categories: legends,
                    labels: { show: false }
                },
                yaxis: {
                    labels: { show: false }
                },
            },
        })
    }

    render() {
        const { options } = this.state;

        return (
            <>
                {options && <Chart
                    options={this.state.options}
                    series={this.state.series}
                    type="area"
                    height={200}
                />}
            </>
        );
    }
}

export class ChartBiens extends Component {
    constructor(props) {
        super(props);

        this.state = {
            series: [],
            options: {}
        }
    }

    componentDidMount = () => {
        const { donnees } = this.props;

        let data = JSON.parse(donnees);
        let last = data.length;

        if(last > 0){
            last = last - 1;
            let biensData = [
                { x: 'Maisons',         y: data[last].nbMaisons },
                { x: 'Appartements',    y: data[last].nbAppartements },
                { x: 'Parkings',        y: data[last].nbParkings },
                { x: 'Bureaux',         y: data[last].nbBureaux },
                { x: 'Locaux',          y: data[last].nbLocaux },
                { x: 'Immeubles',       y: data[last].nbImmeubles },
                { x: 'Terrains',        y: data[last].nbTerrains },
                { x: 'Commerces',       y: data[last].nbCommerces },
                { x: 'Autres',          y: data[last].nbAutres },
            ];

            this.setState({
                series: [
                    {
                        data: biensData
                    }
                ],
                options: {
                    legend: {
                        show: false
                    },
                    chart: {
                        height: 200,
                        type: 'treemap',
                        toolbar: { show: false },
                    },
                    colors: [
                        '#3B93A5',
                        '#F7B844',
                        '#ADD8C7',
                        '#EC3C65',
                        '#CDD7B6',
                        '#C1F666',
                        '#D43F97',
                        '#1E5D8C',
                        '#421243',
                    ],
                    plotOptions: {
                        treemap: {
                            distributed: true,
                            enableShades: false
                        }
                    }
                },
            })
        }
    }

    render() {
        const { options } = this.state;

        return (
            <>
                {options && <Chart
                    options={this.state.options}
                    series={this.state.series}
                    type="treemap"
                    height={200}
                />}
            </>
        );
    }
}