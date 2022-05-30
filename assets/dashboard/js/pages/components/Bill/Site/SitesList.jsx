import React, { Component } from 'react';

import axios        from "axios";
import Swal         from "sweetalert2";
import SwalOptions  from "@commonComponents/functions/swalOptions";
import Routing      from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Formulaire from "@dashboardComponents/functions/Formulaire";

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import { SitesItem, SitesItemCustomerForm } from "@dashboardPages/components/Bill/Site/SitesItem";

const URL_DELETE_ELEMENT    = 'api_bill_sites_delete';
const URL_DELINK_ELEMENT    = 'api_bill_sites_dissociate';
const MSG_DELETE_ELEMENT    = 'Supprimer ce site ?';
const MSG_DELINK_ELEMENT    = 'Dissocier ce site de ce client ?';

export class SitesList extends Component {
    render () {
        const { data, taille, onChangeContext, onSearch, perPage, onPerPage, onDeleteAll,
            onPaginationClick, currentPage, sorters, onSorter } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un site</Button>
                    </div>
                    <div className="item filter-search">
                        <Search onSearch={onSearch} placeholder="Recherche par nom"/>
                    </div>
                </div>

                <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-header-selector" />
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Site</div>
                                        <div className="col-2">Adresse</div>
                                        <div className="col-3">Client</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <SitesItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                {(data && data.length !== 0) && <div className="page-actions">
                    <div className="selectors-actions">
                        <div className="item" onClick={onDeleteAll}>
                            <ButtonIcon icon="trash" text="Supprimer la sélection" />
                        </div>
                    </div>
                </div>}
            </div>
        </>
    }
}

export class SitesListCustomerForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            context: "create",
            element: null,
            sorter: props.sorter,
        }

        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDelink = this.handleDelink.bind(this);
    }

    handleChangeContext = (context, element = null) => { this.setState({ context, element }) }

    handleDelete = (element) => { deleteOrDelink(this, MSG_DELETE_ELEMENT, "DELETE", URL_DELETE_ELEMENT, element); }

    handleDelink = (element) => { deleteOrDelink(this, MSG_DELINK_ELEMENT, "PUT", URL_DELINK_ELEMENT, element); }

    render () {
        const { data, societyId, onUpdateList, customer, sites, onAssociateSite } = this.props;
        const { context, element } = this.state;

        return <>
            <div className="sites-list-customer-form">
                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-3">
                                        <div className="col-1">Site</div>
                                        <div className="col-2">Adresse</div>
                                        <div className="col-3 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <SitesItemCustomerForm {...this.props} elem={elem} key={elem.id}
                                                          onDelete={this.handleDelete} onDelink={this.handleDelink}
                                                          onChangeContext={this.handleChangeContext} />
                        }) : <Alert>Aucun résultat</Alert>}

                        {/*<CustomerSelectForm sites={sites} onAssociateSite={onAssociateSite} />*/}

                        {/*{<SiteFormulaire type={context} typeForm="customer" societyId={societyId} customer={customer}*/}
                        {/*                 element={element} onChangeContext={this.handleChangeContext} onUpdateList={onUpdateList} />}*/}
                    </div>
                </div>
            </div>
        </>
    }
}

function deleteOrDelink (self, title, method, routeName, element, msg="Cette action est irréversible.") {
    Swal.fire(SwalOptions.options(title, msg))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: method, url: Routing.generate(routeName, {'id': element.id}), data: {} })
                    .then(function (response) {
                        self.props.onUpdateList(element, "delete");
                    })
                    .catch(function (error) {
                        Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
                    })
                    .then(() => {
                        Formulaire.loader(false);
                    })
                ;
            }
        })
    ;
}
