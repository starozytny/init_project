import React, { Component } from 'react';

import Sanitize from "@dashboardComponents/functions/sanitaze";
import { Gallery } from "./split/Gallery";

export class AdsItem extends Component {
    render () {
        const { elem, onChangeContext } = this.props

        return <div className="item">
            <div className="selector">
                <div>#{elem.id}</div>
            </div>

            <div className="item-content" onClick={() => onChangeContext("show", elem)}>
                <div className="item-body">
                    <div className="avatar">
                        <Gallery elem={elem} isImage={true}/>
                    </div>
                    <div className="infos" onClick={() => onChangeContext("show", elem)}>
                        <div className="ad-info">
                            <div className="name">
                                <div>{elem.label}</div>
                            </div>
                            <div className="sub sub-username">{elem.address.zipcode}, {elem.address.city}</div>
                            {/*<div className="sub sub-username">{elem.agency.name}</div>*/}
                            <div className="sub">{elem.ref} - {elem.realRef}</div>
                        </div>
                        <div className="ad-price">
                            <div>{Sanitize.toFormatCurrency(elem.financial.price)} {elem.typeAd === "Location" && "cc/mois"}</div>
                        </div>
                        <div className="ad-ad">
                            <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.codeTypeAd} {elem.typeAd}</div>
                        </div>
                        <div className="ad-bien">
                            <div className="role type-bien">{elem.typeBien}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}