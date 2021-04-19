import React, { Component } from 'react';

export class AdsSearch extends Component {
    render () {
        const { nature, onNature } = this.props;

        let natureItems = [
            {id: 0, label: "Location", slug: "location"},
            {id: 1, label: "Vente", slug: "vente"}
        ]

        return <>
            <div className="ads-search-nature">
                {natureItems.map(el => {
                    let active = el.label === nature ? " active" : "";
                    return <div className={"ads-search-nature-" + el.slug + active} onClick={() => onNature(el.label)} key={el.id}>
                        {el.label}
                    </div>
                })}
            </div>
        </>
    }
}