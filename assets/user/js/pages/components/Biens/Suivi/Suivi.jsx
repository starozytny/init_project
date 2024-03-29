import React, { Component } from 'react';

import Routing  from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Rapprochements } from "@userPages/components/Biens/Suivi/Rapprochements";
import { Global }         from "@userPages/components/Biens/Suivi/Global";
import { Visits }         from "@dashboardPages/components/Immo/Visits/Visits";
import { AdBadges, AdMainInfos } from "@userPages/components/Biens/Read/AdItem";

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

import DataState from "@userPages/components/Biens/Form/data";

export class Suivi extends Component {
    constructor(props) {
        super(props);

        this.state = {
            elem: JSON.parse(props.elem),
            suivis: props.suivis ? JSON.parse(props.suivis) : [],
            negotiators: props.negotiators ? JSON.parse(props.negotiators) : [],
            allVisits: props.visits ? JSON.parse(props.visits) : [],
            context: props.context ? props.context : "global",
        }

        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleUpdateVisits = this.handleUpdateVisits.bind(this);
    }

    handleChangeContext = (context) => { this.setState({ context }) }

    handleUpdateVisits = () => {
        DataState.getVisits(this, this.state.elem);
    }

    render () {
        const { elem, context, suivis, negotiators, allVisits } = this.state;

        let content;
        switch (context){
            case "offres":
                content = <div>Offres</div>
                break;
            case "rapprochements":
                content = <Rapprochements elem={elem} data={suivis} societyId={elem.agency.society.id} agencyId={elem.agency.id} negotiators={negotiators} />
                break;
            case "visites":
                content = <Visits bienId={elem.id} donnees={JSON.stringify(allVisits)} onUpdateVisits={this.handleUpdateVisits} isSuiviPage={true} classes={""}/>
                break;
            default:
                content = <Global elem={elem} suivis={suivis} visits={allVisits} />
                break;
        }

        return <div className="main-content">
            <div className="details-container">
                <div className="details-content-container suivi-container">
                    <AdMainInfos elem={elem} />
                    <div className="details-general">
                        <AdBadges elem={elem} />
                        <div className="details-ad-actions">
                            <ButtonIcon element="a" icon="vision" onClick={Routing.generate('user_biens_read', {'slug': elem.slug})}>Détails</ButtonIcon>
                        </div>
                    </div>
                    <Navigation context={context} onChangeContext={this.handleChangeContext} />
                    <div className="details-content">
                        {content}
                    </div>
                </div>
            </div>
        </div>
    }
}

function Navigation({ onChangeContext, context }){

    let items = [
        {context: "global",            label: "Global"},
        {context: "visites",           label: "Visites"},
        {context: "rapprochements",    label: "Rapprochements"},
        {context: "offres",            label: "Offres"},
    ]

    return (
        <div className="details-nav">
            <div className="details-nav-items">
                {items.map(el => {
                    return <div className={context === el.context ? "active" : ""} onClick={() => onChangeContext(el.context)} key={el.context}>{el.label}</div>
                })}
            </div>
        </div>
    )
}