import React, { Component } from 'react';

import { Button }        from "@dashboardComponents/Tools/Button";

export class DemandeRead extends Component {
    render () {
        const { element, onChangeContext } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item">
                        <Button outline={true} icon="left-arrow" type="primary" onClick={() => onChangeContext("list")}>Retour à la liste</Button>
                    </div>
                </div>

                <div className="item-data-read">
                    <div className="name">{element.name}</div>
                    <div className="sub">{element.email}</div>
                    <div className="sub">{element.phone}</div>
                    <div className="sub sub-time">{element.createdAtAgo}</div>
                    <div className="sub-message">{element.message}</div>
                </div>

                <div className="item-data-read item-data-bien">
                    {element.bien ? <div>
                        <div className="name">{element.bien.label}</div>
                        <div>{element.bien.address.address}</div>
                        <div>{element.bien.address.shortAddress}</div>
                        <div className="sub">{element.bien.realRef}</div>
                    </div> : <div>
                        <div className="name">Bien supprimé</div>
                        <div>{element.label}</div>
                        <div>{element.address}</div>
                        <div>{element.shortAddress}</div>
                        <div className="sub">{element.realRef}</div>
                    </div>}
                </div>
            </div>
        </>
    }
}