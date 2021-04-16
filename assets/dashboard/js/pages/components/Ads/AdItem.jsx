import React, { Component } from 'react';

import Sanitize from "@dashboardComponents/functions/sanitaze";
import { Button } from "@dashboardComponents/Tools/Button";

export class AdItem extends Component {
    constructor(props) {
        super(props);

        this.state = {
            subContext: "infos"
        }

        this.handleChangeSubContext = this.handleChangeSubContext.bind(this);
    }

    handleChangeSubContext = (subContext) => { this.setState({ subContext }) }

    render () {
        const { elem, onChangeContext } = this.props
        const { subContext } = this.state;

        let content = null;
        switch (subContext){
            case "financial":
                content = <div>Financial</div>
                break;
            case "diag":
                content = <div>Diag</div>
                break;
            case "features":
                content = <div>Features</div>
                break;
            default:
                content = <div>Infos</div>
                break;
        }

        return <div>
            <div className="toolbar">
                <div className="item">
                    <Button icon="left-arrow" outline={true} onClick={() => onChangeContext("list")}>Retour</Button>
                </div>
            </div>

            <div className="details-content">
                <div className="details-pretitle">{elem.agency.name}</div>
                <div className="details-title">{elem.label}</div>
                <div className="details-subtitle">
                    <div>{elem.address.zipcode}, {elem.address.city}</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.price)}</div>
                </div>
                <div className="details-general">
                    <div className="details-ad-types">
                        <div className={"role type-ad ad-" + elem.codeTypeAd}>{elem.typeAd}</div>
                        <div className="role type-bien">{elem.typeBien}</div>
                    </div>
                </div>
                <Navigation onChangeContext={this.handleChangeSubContext} />
                <div className="details-content">
                    {content}
                </div>
            </div>
        </div>
    }
}

function Navigation({ onChangeContext }){
    return (
        <div className="details-nav">
            <div className="details-nav-items">
                <div className="active" onClick={() => onChangeContext("infos")}>Infos</div>
                <div onClick={() => onChangeContext("features")}>Caractéristique</div>
                <div onClick={() => onChangeContext("diag")}>Diagnostic</div>
                <div onClick={() => onChangeContext("financial")}>Financier</div>
            </div>
        </div>
    )
}