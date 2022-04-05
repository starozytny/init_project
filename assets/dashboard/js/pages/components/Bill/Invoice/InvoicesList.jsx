import React, { Component } from 'react';

import axios             from "axios";
import toastr            from "toastr";
import Swal              from "sweetalert2";
import SwalOptions       from "@commonComponents/functions/swalOptions";
import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { Button }   from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { Filter, FilterSelected } from "@dashboardComponents/Layout/Filter";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import { InvoicesItem }   from "@dashboardPages/components/Bill/Invoice/InvoicesItem";

const URL_GENERATE_INVOICE = "api_bill_invoices_generate";

export class InvoicesList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            dateInvoice: new Date(props.society.dateInvoiceJavascript)
        }

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
        this.handleGenerate = this.handleGenerate.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    handleGenerate = (elem) => {
        const { dateInvoice } = this.state;

        if(dateInvoice){
            let dateAt = new Date(elem.dateAtJavascript);
            dateAt.setHours(0, 0, 0);
            dateInvoice.setHours(0, 0, 0);

            if(dateAt < dateInvoice){
                askDate(this, elem, dateInvoice);
            }else{
                generateInvoice(this, elem, dateAt)
            }
        }
    }

    render () {
        const { data, onChangeContext, taille, onGetFilters, filters, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter } = this.props;

        let filtersLabel = ["Brouillon", "A régler", "Payée", "Partiel", "Archivée"];
        let filtersId    = ["f-br", "f-are", "f-pa", 'f-pa', "f-arc"];

        let itemsFilter = [
            { value: 0, id: filtersId[0], label: filtersLabel[0] },
            { value: 1, id: filtersId[1], label: filtersLabel[1] },
            { value: 2, id: filtersId[2], label: filtersLabel[2] },
            { value: 3, id: filtersId[3], label: filtersLabel[3] },
            { value: 4, id: filtersId[4], label: filtersLabel[4] },
        ];

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une facture</Button>
                    </div>
                    <div className="item filter-search">
                        <Filter ref={this.filter} items={itemsFilter} onGetFilters={onGetFilters} />
                        <Search onSearch={onSearch} placeholder="Recherche par numéro ou nom"/>
                        <FilterSelected filters={filters} itemsFiltersLabel={filtersLabel} itemsFiltersId={filtersId} onChange={this.handleFilter}/>
                    </div>
                </div>

                <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-7">
                                        <div className="col-1">Numéro</div>
                                        <div className="col-2">Client</div>
                                        <div className="col-3">Date</div>
                                        <div className="col-4">Date échéance</div>
                                        <div className="col-5">Montant TTC</div>
                                        <div className="col-6">Statut</div>
                                        <div className="col-7 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <InvoicesItem {...this.props} onGenerate={this.handleGenerate} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}

function askDate(self, elem, nDateCreancier)
{
    let flatpickrInstance;
    let nDateCreancierString = nDateCreancier.toLocaleDateString("fr");
    let msg = `La date ne peut pas être inférieur à ${nDateCreancierString}`;

    Swal.fire({
        title: 'Veuillez saisir une date de facturation.',
        html: '<div>' +
            'Afin de respecter la cohérence entre la date et le numéro de facturation, ' +
            'vous ne pouvez pas établir une facture avec la date de formation. <br><br>' +
            'La date doit être supérieure ou égale à ' + nDateCreancierString + "." +
            '</div>' +
            '<input class="swal2-input" id="expiry-date">',
        stopKeydownPropagation: false,
        preConfirm: () => {
            nDateCreancier.setHours(0,0,0);
            if (nDateCreancier && flatpickrInstance.selectedDates[0] < nDateCreancier) {
                Swal.showValidationMessage(msg)
            }
        },
        onOpen: () => {
            if(nDateCreancier){
                flatpickrInstance = flatpickr('#expiry-date', {
                    'dateFormat': 'd/m/Y',
                    'minDate': nDateCreancier
                });
            }else{
                flatpickrInstance = flatpickr('#expiry-date', {
                    'dateFormat': 'd/m/Y',
                });
            }
        }
    })
        .then((result) => {
            if (result.isConfirmed) {
                if(flatpickrInstance.selectedDates[0] >= nDateCreancier){
                    generateInvoice(self, elem, flatpickrInstance.selectedDates[0])
                }else{
                    Swal.showValidationMessage(msg)
                }
            }else{
                Swal.showValidationMessage(msg)
            }
        })
}

function generateInvoice(self, elem, dateAt)
{
    Swal.fire(SwalOptions.options("Finaliser la facture",
        "Une fois finalisée, la facture <u>ne pourra plus être modifiée</u>. " +
            "En cas d'erreur sur la facture, il faudra faire un avoir pour la rectifier. <br><br>" +
            "<b>Un mail sera envoyé aux adhérents pour les notifier de la création de leur(s) facture(s)</b>"))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);
                axios({ method: "POST", url: Routing.generate(URL_GENERATE_INVOICE, {'id': elem.id}), data: {dateAt: dateAt} })
                    .then(function (response) {
                        let data = response.data;
                        if (self.props.onUpdateList) {
                            console.log(data);
                            self.props.onUpdateList(data, "update")
                        }
                        toastr.info("Facture générée avec succès.")
                        self.setState({ dateInvoice: dateAt })
                    })
                    .catch(function (error) {
                        Formulaire.displayErrors(self, error);
                    })
                    .then(function () {
                        Formulaire.loader(false);
                    })
                ;
            }
        })
    ;
}
