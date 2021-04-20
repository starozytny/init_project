import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

import { Alert }      from "@dashboardComponents/Tools/Alert";

import { AdsItem }   from "./AdsItem";
import { AdsSearch } from "./AdsSearch";
import { MapGroup }  from "./split/MapGroup";

export class AdsList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            context: "list",
            nature: "Location"
        }

        this.handleNature = this.handleNature.bind(this);
        this.handleChangeContext = this.handleChangeContext.bind(this);
    }

    handleNature = (label) => { this.setState({ nature: label }) }
    handleChangeContext = (context) => { this.setState({ context }) }

    render () {
        const { data } = this.props;
        const { context, nature } = this.state;

        let currentData = [];
        if(data && data.length !== 0){
            data.forEach(elem => {
                if (elem.typeAd === nature) {
                    currentData.push(elem);
                }
            })
        }

        let contexts = [
            {id: 0, icon: 'tasks', context: 'list'},
            {id: 1, icon: 'placeholder', context: 'map'}
        ]

        console.log(context)

        return <>
            <div>
                <div className="toolbar">
                    <div className="item display-mode">
                        {contexts.map(c => {
                            let active = context === c.context ? " active" : "";
                            return <div className={active} onClick={() => this.handleChangeContext(c.context)} key={c.id}>
                                <span className={"icon-" + c.icon} />
                            </div>
                        })}
                    </div>
                    <div className="item ads-search">
                        <AdsSearch {...this.state} onNature={this.handleNature}/>
                    </div>
                </div>

                {context === "map" ? <MapGroup elems={currentData}/> : <>
                    <div className="items-table">
                        <div className="items items-default items-user items-ad">
                            <div className="item item-header">
                                <div className="selector">#</div>
                                <div className="item-content">
                                    <div className="item-body">
                                        <div className="avatar" />
                                        <div className="infos">
                                            <div className="ad-info">Bien</div>
                                            <div className="ad-price">Prix</div>
                                            <div className="ad-ad">Type annonce</div>
                                            <div className="ad-bien">Type bien</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {currentData && currentData.length !== 0 ? currentData.map(elem => {
                                return <AdsItem {...this.props} elem={elem} key={elem.id}/>
                            }) : <Alert>Aucun résultat</Alert>}
                        </div>
                    </div>
                </>}

                <div className="page-actions">
                    <div className="common-actions">
                        <div className="item">
                            <div className="dropdown">
                                <div className="dropdown-btn">
                                    <span className="icon-download" />
                                    <span>Exporter</span>
                                </div>
                                <div className="dropdown-items">
                                    <a className="item" download="utilisateurs.csv" href={Routing.generate('api_users_export', {'format': 'csv'})}>
                                        <ButtonIcon icon="file" text="Exporter en CSV" />
                                    </a>
                                    <a className="item" download="utilisateurs.xlsx" href={Routing.generate('api_users_export', {'format': 'excel'})}>
                                        <ButtonIcon icon="file" text="Exporter en Excel" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </>
    }
}