import React, { Component } from 'react';

import { Alert }      from "@dashboardComponents/Tools/Alert";

import { AdsItem }   from "./AdsItem";
import { AdsSearch } from "./AdsSearch";
import { MapGroup }  from "./split/MapGroup";

export class AdsList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            context: "list",
        }

        this.handleChangeContext = this.handleChangeContext.bind(this);
    }

    handleChangeContext = (context) => { this.setState({ context }) }

    render () {
        const { data, currentData, onChangeNature } = this.props;
        const { context } = this.state;

        let contexts = [
            {id: 0, icon: 'tasks', context: 'list'},
            {id: 1, icon: 'placeholder', context: 'map'}
        ]

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
                        <AdsSearch {...this.props} onNature={onChangeNature}/>
                    </div>
                </div>

                {context === "map" ? <MapGroup elems={data} /> : <>
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
            </div>
        </>
    }
}