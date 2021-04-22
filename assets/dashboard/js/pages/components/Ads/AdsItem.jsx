import React, { Component } from 'react';

import Sanitize from "@dashboardComponents/functions/sanitaze";
import { GalleryThumbs } from "./split/Gallery";

export class AdsItem extends Component {
    render () {
        const { elem, onChangeContext } = this.props

        // return <div className="item" onClick={() => onChangeContext("show", elem)}>
        return <div className="item">
            <div className="selector">
                <div>#{elem.id}</div>
            </div>

            <div className="item-content">
                <div className="item-body">
                    <div className="avatar">
                        <GalleryThumbs elem={elem}/>
                        {/*{elem.thumb ? <img src={`/annonces/thumbs/${elem.agency.dirname}/${elem.thumb}`} alt={`Image de ${elem.ref}`}/>*/}
                        {/*    : <img src={`https://robohash.org/${elem.ref}?size=150x150`} alt={`Image de ${elem.ref}`}/>}*/}
                    </div>
                    <div className="infos">
                        <div className="ad-info">
                            <div className="name">
                                <div>{elem.label}</div>
                            </div>
                            <div className="sub sub-username">{elem.address.zipcode}, {elem.address.city}</div>
                            <div className="sub sub-username">{elem.agency.name}</div>
                            <div className="sub">{elem.ref} - {elem.realRef}</div>
                        </div>
                        <div className="ad-price">
                            <div>{Sanitize.toFormatCurrency(elem.financial.price)} {elem.typeAd === "Location" && "/ mois"}</div>
                        </div>
                        <div className="ad-ad">
                            <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.typeAd}</div>
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