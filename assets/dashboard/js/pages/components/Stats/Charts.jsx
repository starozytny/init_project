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
                colors: ['#109cf1', '#fdad2d', '#f7685b'],
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