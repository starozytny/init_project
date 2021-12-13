import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class FormationsItem extends Component {
    render () {
        const { elem } = this.props

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-3">
                        <div className="col-1">
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                            {elem.rating && <div className="rating">{elem.rating} <span className="icon-star-2" /></div> }
                        </div>
                        <div className="col-2">
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