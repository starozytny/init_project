import React, { Component } from 'react';

import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

import Sanitaze         from "@commonComponents/functions/sanitaze";

export class LotsItem extends Component {
    render () {
        const { isDeveloper, elem, onDelete, onSelectors } = this.props

        return <div className="item">
            {isDeveloper && <Selector id={elem.id} onSelectors={onSelectors} />}

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="name">
                                <span>{elem.datePaiementString}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div>{Sanitaze.toFormatCurrency(elem.total)}</div>
                            <div className="sub">{elem.nbOfTxs} transac.</div>
                        </div>

                        <div className="col-3">
                            <div className="sub">
                                <a target="_blank" download={elem.filename} href={Routing.generate('api_lots_file', {'id': elem.id})}>
                                    {elem.filename}
                                </a>
                            </div>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon element="a" target="_blank" icon="file" onClick={Routing.generate('api_lots_bordereau', {'id': elem.id})}>Bordereau</ButtonIcon>
                            {isDeveloper && <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}