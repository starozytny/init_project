import React, { Component } from 'react';

import { Alert } from "@dashboardComponents/Tools/Alert";

export class Search extends Component {
    constructor() {
        super();

        this.state = {
            search: ""
        }

        this.handleChange = this.handleChange.bind(this);
    }

    // from fo uncheck via toolbar filter checked
    handleChange = (e) => {
        let val = e.currentTarget.value;
        this.setState({search: val});

        this.props.onSearch(val);
    }

    render () {
        const { search } = this.state;

        return <div className={`search ${search !== "" ? " active" : ""}`}>
            <input type="search" name="search" id="search" value={search} placeholder="Recherche..." onChange={this.handleChange} />
            <span className="icon-search" />
        </div>
    }
}