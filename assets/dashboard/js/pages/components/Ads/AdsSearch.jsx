import React, { Component } from 'react';

import { Button } from "@dashboardComponents/Tools/Button";

export class AdsSearch extends Component {
    constructor(props) {
        super(props);

        this.state = {
            nature: ""
        }

        this.handleNature = this.handleNature.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
    }

    handleNature = (slug) => { this.setState({ nature: slug }) }

    handleSearch = () => {

    }

    render () {
        const { nature } = this.state;

        let natureItems = [
            {id: 0, label: "Location", slug: "location"},
            {id: 1, label: "Vente", slug: "vente"}
        ]

        return <>
            <div className="ads-search-nature">
                {natureItems.map(el => {
                    let active = el.slug === nature ? " active" : "";
                    return <div className={"ads-search-nature-" + el.slug + active} onClick={() => this.handleNature(el.slug)} key={el.id}>
                        {el.label}
                    </div>
                })}
            </div>
            <div className="ads-search-apply" onClick={this.handleSearch}>
                <Button >Appliquer</Button>
            </div>
        </>
    }
}