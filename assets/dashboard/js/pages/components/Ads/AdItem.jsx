import React, { Component } from 'react';

import Sanitize from "@dashboardComponents/functions/sanitaze";
import { Button } from "@dashboardComponents/Tools/Button";

export class AdItem extends Component {
    render () {
        const { elem, onChangeContext } = this.props

        return <div>
            <div className="toolbar">
                <div className="item">
                    <Button icon="left-arrow" outline={true} onClick={() => onChangeContext("list")}>Retour</Button>
                </div>
            </div>

            <div className="details-content">
                <div className="details-title">{elem.label}</div>
                <div className="details-subtitle">
                    <div className="details-ad-types">
                        <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.typeAd}</div>
                        <div className="role type-bien">{elem.typeBien}</div>
                    </div>
                    <div className="details-ad-address">
                        {elem.address.zipcode}, {elem.address.city}
                    </div>
                    <div className="details-ad-price">
                        {Sanitize.toFormatCurrency(elem.financial.price)}
                    </div>
                    <div className="details-ad-agency">
                        {elem.agency.name}
                    </div>
                </div>
            </div>
        </div>
    }
}