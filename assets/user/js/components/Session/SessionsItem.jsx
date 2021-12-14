import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class SessionsItem extends Component {
    render () {
        const { elem } = this.props;

        console.log(elem)
        let formation = elem.formation;

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-3">
                        <div className="col-1">
                            <div className="name">
                                <span>{formation.name}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            {elem.startJavascript}
                        </div>
                        <div className="col-3 actions">
                            <ButtonIcon icon="download">S'inscrire</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}