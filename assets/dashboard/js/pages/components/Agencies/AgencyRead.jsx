import React, { Component } from 'react';

import { Button }        from "@dashboardComponents/Tools/Button";

export class AgencyRead extends Component {
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
                </div>
            </div>
        </>
    }
}