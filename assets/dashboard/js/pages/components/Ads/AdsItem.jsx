import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

export class AdsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors } = this.props

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="avatar">
                        {elem.thumb ? <img src={`/annonces/thumbs/${elem.agency.dirname}/${elem.thumb}`} alt={`Image de ${elem.ref}`}/> : <img src={`https://robohash.org/${elem.ref}?size=150x150`} alt={`Image de ${elem.ref}`}/>}

                    </div>
                    <div className="infos">
                        <div>
                            <div className="name">
                                <span>{elem.label}</span>
                                <span className="role">{elem.typeAd} - {elem.typeBien}</span>
                            </div>
                            <div className="sub sub-username">{elem.ref} - {elem.realRef}</div>
                        </div>
                        <div className="actions">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}