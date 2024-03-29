import React, { Component } from 'react';

import axios   from "axios";
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Formulaire from "@dashboardComponents/functions/Formulaire";
import Validateur from "@commonComponents/functions/validateur";

import { Button }        from "@dashboardComponents/Tools/Button";
import { Alert }         from "@dashboardComponents/Tools/Alert";
import { LoaderElement } from "@dashboardComponents/Layout/Loader";
import { Input }         from "@dashboardComponents/Tools/Fields";

import { AdCard }        from "@userPages/components/Biens/AdCard";
import { SearchsItem }   from "@dashboardPages/components/Immo/Searchs/SearchsItem";

function getData(self, context, elem, data={}) {
    axios({ method: "POST", url: Routing.generate('api_searchs_results', {'id': elem.id}), data: data })
        .then(function (response) {
            let data = response.data;
            if(context === "main"){
                self.setState({ loadData: false, context: context, data: data })
            }else{
                self.setState({ loadData: false, context:context, data2: data })
            }
        })
        .catch(function (error) {
            Formulaire.displayErrors(self, error);
        })
    ;
}

export class SearchRead extends Component {
    constructor(props) {
        super();

        this.state = {
            context: 'main',
            loadData: true,
            data: [],
            data2: [],
            price: props.elem.codeTypeAd === 1 ? 100 : 20000,
            piece: 1,
            room: 1,
            area: 30,
            land: 30,
            errors: []
        }

        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleLink = this.handleLink.bind(this);
    }

    componentDidMount = () => { getData(this, "main", this.props.elem); }

    handleChangeContext = (context) => {
        const { price, piece, room, area, land } = this.state;

        if(context === "main"){
            this.setState({ context })
        }else{
            this.setState({ errors: [] })

            let data = { price: price, piece: piece, room: room, area: area, land: land }

            let paramsToValidate = [
                {type: "text", id: 'price', value: price},
                {type: "text", id: 'piece', value: piece},
                {type: "text", id: 'room',  value: room},
                {type: "text", id: 'area',  value: area},
                {type: "text", id: 'land',  value: land},
            ];

            // validate global
            let validate = Validateur.validateur(paramsToValidate)
            if(!validate.code){
                Formulaire.showErrors(this, validate);
            }else{
                getData(this, context, this.props.elem, data)
            }
        }
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleLink = (bien) => {
        const { follows } = this.props;

        let method = "POST";
        let url = Routing.generate('api_suivis_link_bien', {'id': bien.id});
        follows.forEach(follow => {
            if(follow.bien.id === bien.id){
                method = "DELETE"
                url = Routing.generate('api_suivis_delete', {'id': this.props.prospectId, 'bien': bien.id})
            }
        })

        const self = this;
        axios({ method: method, url: url, data: [{id: this.props.prospectId}] })
            .then(function (response) {
                self.props.onUpdateFollows(bien, method === "POST" ? "create" : "delete");
            })
            .catch(function (error) {
                Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
            })
        ;
    }

    render () {
        const { onChangeContext, follows, elem } = this.props;
        const { loadData, context, data, data2, errors, price, piece, room, area, land } = this.state;

        return <div className="page-default">
            <div className="page-col-1">
                <div className="body-col-1">
                    <div className="title-col-1">
                        <span>Recherche de base :</span>
                    </div>
                    <div className="content-col-1">
                        <Button type={context === "main" ? "primary" : "default"}
                                onClick={() => this.handleChangeContext("main")}>
                            Recherche simple
                        </Button>
                    </div>
                    <div className="title-col-1">
                        <span>Elargir la recherche :</span>
                    </div>
                    <div className="content-col-1">
                        <div className="line">
                            <Input type="number" step="any" identifiant="price" valeur={price} errors={errors} onChange={this.handleChange}>
                                Delta prix
                            </Input>
                        </div>
                        <div className="line">
                            <Input type="number" step="any" identifiant="piece" valeur={piece} errors={errors} onChange={this.handleChange}>
                                Delta pièces
                            </Input>
                        </div>

                        <div className="line">
                            <Input type="number" step="any" identifiant="room" valeur={room} errors={errors} onChange={this.handleChange}>
                                Delta chambres
                            </Input>
                        </div>

                        <div className="line">
                            <Input type="number" step="any" identifiant="area" valeur={area} errors={errors} onChange={this.handleChange}>
                                Delta surface habitable
                            </Input>
                        </div>


                        <div className="line">
                            <Input type="number" step="any" identifiant="land" valeur={land} errors={errors} onChange={this.handleChange}>
                                Delta surface terrain
                            </Input>
                        </div>

                        <Button type={context !== "main" ? "primary" : "default"}
                                onClick={() => this.handleChangeContext("second")}>
                            Recherche avancée
                        </Button>
                    </div>
                </div>
            </div>
            <div className="page-col-2">
                <div>
                    <div className="items-table">
                        <div className="items items-default">
                            <div className="item item-header">
                                <div className="item-content">
                                    <div className="item-body">
                                        <div className="infos infos-col-5">
                                            <div className="col-1">Type</div>
                                            <div className="col-2">Localisation</div>
                                            <div className="col-3">Informations</div>
                                            <div className="col-4">Avantages</div>
                                            <div className="col-5 actions" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <SearchsItem elem={elem} />
                        </div>
                    </div>
                </div>
                <div>
                    {loadData ? <LoaderElement /> : <DataBiens data={context === "main" ? data : data2} follows={follows} onLink={this.handleLink} />}
                </div>
            </div>
        </div>
    }
}

function DataBiens ({ data, follows, onLink }) {
    return <div className="content">
        {data.length !== 0 ? data.map(el => {
            return <AdCard el={el} isProspectPage={true} onLinkToProspect={onLink} follows={follows} key={el.id}/>
        }) : <Alert>Aucun résultat</Alert>}
    </div>
}