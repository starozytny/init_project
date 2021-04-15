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
                        {/*<img src={`https://robohash.org/${elem.username}?size=64x64`} alt={`Avatar de ${elem.username}`}/>*/}
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