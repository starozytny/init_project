import React, { Component } from 'react';

import axios        from "axios";
import toastr       from "toastr";
import { uid }      from "uid";
import Routing      from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button }   from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Aside }    from "@dashboardComponents/Tools/Aside";
import { Search }   from "@dashboardComponents/Layout/Search";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import Formulaire from "@dashboardComponents/functions/Formulaire";
import Sanitaze   from "@commonComponents/functions/sanitaze";
import helper     from "@dashboardPages/components/Bill/functions/helper";

import { ContractsItem, ContractsItemProcess } from "@dashboardPages/components/Bill/Contract/ContractsItem";
import { InvoiceGenerateFormulaire } from "@dashboardPages/components/Bill/Invoice/InvoiceGenerate";
import { Customers } from "@dashboardPages/components/Bill/Customer/Customers";
import { ContractsGenerate } from "@dashboardPages/components/Bill/Contract/ContractsGenerate";

const URL_INDEX             = "admin_bill_contracts_process";
const URL_CREATE_INVOICE    = "api_bill_contracts_invoice_create";
const URL_GENERATE_INVOICE  = "api_bill_contracts_invoice_generate";
const URL_GENERATE_INVOICES = "api_bill_contracts_invoices_generate";

const MONTHS = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

const STATUS_DRAFT = 0;
const STATUS_TO_PAY = 1;
const STATUS_PAID = 2;
const STATUS_PAID_PARTIAL = 3;

export class ContractsList extends Component {
    constructor(props) {
        super();

        this.state = {
            element: null
        }

        this.aside = React.createRef();

        this.handleOpenAside = this.handleOpenAside.bind(this);
    }

    handleOpenAside = (element) => {
        this.setState({ element })
        this.aside.current.handleOpen("Sélection des clients pour le contrat " + element.name);
    }

    render () {
        const { data, onChangeContext, taille, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter, customers, sites, relations, onChangeRelation } = this.props;
        const { element } = this.state;

        let contentAside = <Customers donnees={customers} sites={sites} isPageContract={true} classes=" "
                                      onChangeRelation={onChangeRelation} contract={element} relations={relations} />

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un contrat</Button>
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
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Contrat</div>
                                        <div className="col-2">Périodicité</div>
                                        <div className="col-3">Total TTC</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <ContractsItem {...this.props} elem={elem} key={elem.id}
                                                  onOpenAside={this.handleOpenAside} />
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>

            <Aside ref={this.aside} content={contentAside} />
        </>
    }
}

export class ContractsListProcess extends Component {
    constructor(props) {
        super(props);

        this.state = {
            dateInvoice: props.society.dateInvoiceJavascript ? new Date(props.society.dateInvoiceJavascript) : null,
            element: null, //invoice
            dataToGenerate: [],
            invoicesIdToGenerate: [],
            relationsIdToGenerate: [],
        }

        this.asideGenerate = React.createRef();
        this.asideGenerateGeneral = React.createRef();

        this.handleGenerate = this.handleGenerate.bind(this);
        this.handleUpdateDateInvoice = this.handleUpdateDateInvoice.bind(this);
        this.handleCloseAside = this.handleCloseAside.bind(this);
        this.handleUpdateDataToGenerate = this.handleUpdateDataToGenerate.bind(this);
        this.handleUpdateDateElement = this.handleUpdateDateElement.bind(this);
        this.handleCheckGeneral = this.handleCheckGeneral.bind(this);
        this.handleGenerateGeneral = this.handleGenerateGeneral.bind(this);
    }

    handleUpdateDateInvoice = (dateAt) => { this.setState({ dateInvoice: dateAt }) }

    handleCloseAside = () => {
        if(this.asideGenerate.current){
            this.asideGenerate.current.handleClose();
        }
        if(this.asideGenerateGeneral.current){
            this.asideGenerateGeneral.current.handleClose();
        }
    }

    handleGenerate = (relationId, elem, year, month) => {
        const { dateInvoice } = this.state;

        let url = Routing.generate(URL_GENERATE_INVOICE, {'year': year, 'month': month, 'id': relationId})

        if(elem){ // elem is invoice
            helper.checkDatesInvoice(this, 'contract', url, elem, dateInvoice);
        }else{
            const self = this;
            Formulaire.loader(true);
            axios({ method: "POST", url: Routing.generate(URL_CREATE_INVOICE, {'year': year, 'month': month, 'id': relationId}), data: {} })
                .then(function (response) {
                    let data = response.data;
                    helper.checkDatesInvoice(self, 'contract', url, data, dateInvoice);
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(function () {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    handleCheckGeneral = () => {
        const { data, relations, invoices, year, month } = this.props;
        const { dateInvoice } = this.state;

        let refRelation = year+""+month;
        let nData = data, nRelations = relations, nInvoices = invoices;

        //check invoices dates
        let dataToGenerate = [], invoicesIdToGenerate = [], relationsIdToGenerate = [];
        nData.forEach(contract => {
            if(contract.isActive){

                let tmp = {
                    contract: contract,
                    elements: []
                };

                nRelations.forEach(relation => {
                    if(relation.contract.id === contract.id && relation.isActive){
                        let find = false;
                        nInvoices.forEach(invoice => {
                            if(invoice.contractId === contract.id && invoice.relationId === relation.id && invoice.refRelation === refRelation){
                                if(invoice.status === STATUS_DRAFT){
                                    let dateAt = new Date(invoice.dateAtJavascript);
                                    dateAt.setHours(0, 0, 0);

                                    tmp.elements.push({
                                        type: 'invoice',
                                        uid: uid(),
                                        id: invoice.id,
                                        contractId: contract.id,
                                        customer: relation.customer,
                                        dateAt: invoice.dateAtJavascript,
                                        dueAt: invoice.dueAtJavascript,
                                        dueType: invoice.dueType,
                                        isValid: helper.validateDatesInvoice(dateAt, dateInvoice)
                                    });

                                    invoicesIdToGenerate.push(invoice.id);
                                }

                                find = true;
                                nInvoices = nInvoices.filter(el => el.id !== invoice.id);
                                nRelations = nRelations.filter(el => el.id !== relation.id);
                            }
                        })

                        if(!find){
                            let dateAt = new Date(contract.dateAtJavascript);
                            dateAt.setHours(0, 0, 0);

                            tmp.elements.push({
                                type: 'relation',
                                uid: uid(),
                                id: relation.id,
                                contractId: contract.id,
                                customer: relation.customer,
                                dateAt: contract.dateAtJavascript,
                                dueAt: contract.dueAtJavascript,
                                dueType: contract.dueType,
                                isValid: helper.validateDatesInvoice(dateAt, dateInvoice)
                            });

                            relationsIdToGenerate.push(relation.id);
                            nRelations = nRelations.filter(el => el.id !== relation.id);
                        }
                    }
                })

                if(tmp.elements.length !== 0){
                    dataToGenerate.push(tmp)
                }
            }
        })

        this.setState({ dataToGenerate, invoicesIdToGenerate, relationsIdToGenerate })
        this.asideGenerateGeneral.current.handleOpen();
    }

    handleGenerateGeneral = () => {
        const { year, month } = this.props;
        const { dataToGenerate, invoicesIdToGenerate, relationsIdToGenerate } = this.state;

        let errors = false;
        dataToGenerate.forEach(el => {
            el.elements.forEach(elem => {
                if(!elem.isValid){
                    errors = true;
                }
            })
        })

        if(errors){
            toastr.error('Veuillez valider les dates restantes.');
        }else{
            Formulaire.loader(true);
            axios.post(Routing.generate(URL_GENERATE_INVOICES, {'year': year, 'month': month}), {
                data: dataToGenerate,
                invoicesId: invoicesIdToGenerate,
                relationsId: relationsIdToGenerate,
            })
                .then(function (response) {
                    let data = response.data;

                    toastr.info("Génération réussie.")
                    setTimeout(() => {
                        location.reload()
                    }, 2000)
                })
                .catch(function (error) {
                    Formulaire.loader(false);
                    Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
                })
            ;
        }
    }

    handleUpdateDataToGenerate = (element, data) => {
        const { dataToGenerate } = this.state;

        let nData = [];
        dataToGenerate.forEach(el => {
            if(el.contract.id === element.contractId){
                el.elements.forEach(elem => {
                    if(elem.uid === element.uid){
                        elem.dateAt = data.dateAt;
                        elem.dueAt = data.dueAt;
                        elem.dueType = data.dueType;
                        elem.isValid = true;
                    }
                })
            }
            nData.push(el);
        })

        this.setState({ dataToGenerate: nData })
    }

    handleUpdateDateElement = (element) => {
        const { dataToGenerate } = this.state;

        let nData = [];
        dataToGenerate.forEach(el => {
            if(el.contract.id === element.contractId){
                el.elements.forEach(elem => {
                    if(elem.uid === element.uid){
                        elem.isValid = false;
                    }
                })
            }
            nData.push(el);
        })

        this.setState({ dataToGenerate: nData })
    }

    render () {
        const { data, taille, onSearch, perPage, onPerPage, onPaginationClick, currentPage, sorters, onSorter,
            year, month, society, onUpdateList, onUpdateInvoices } = this.props;
        const { element, dateInvoice, dataToGenerate } = this.state;

        let [yearToday, monthToday] = getDateValues(new Date());
        let [yearContract, monthContract] = getDateValues(new Date(society.dateContractJavascript));
        let [yearTodayPlus, monthTodayPlus] = helper.getDatePlusOne(yearToday, monthToday);

        let enableItemAction = false;
        let generateAll = <>
            <div><b>Ce mois n'a pas encore débuté.</b></div>
        </>;

        if(isInferiorOf(year, month, yearToday, monthToday) || (year <= yearContract && month <= monthContract)){
            enableItemAction = true;
            generateAll = <div><b>Historique de {Sanitaze.addZeroToNumber(month)}/{year}</b></div>
        }else if(year === yearToday && month === monthToday
            || (yearContract === yearToday && monthContract === monthToday && year === yearTodayPlus && month === monthTodayPlus)
        ){
            enableItemAction = true;
            generateAll = <>
                <div><b>Génération pour {Sanitaze.addZeroToNumber(month)}/{year}</b></div>
                <div><Button onClick={this.handleCheckGeneral}>Générer les factures</Button></div>
            </>
        }

        let contentGenerate = <InvoiceGenerateFormulaire type="reload" onUpdateList={onUpdateList} onCloseAside={this.handleCloseAside}
                                                         onUpdateDateInvoice={this.handleUpdateDateInvoice} dateInvoice={dateInvoice}
                                                         element={element} key={element ? element.id : 1}
                                                         onUpdateInvoices={onUpdateInvoices} />

        let contentGenerateGeneral = <ContractsGenerate month={month} data={dataToGenerate} dateInvoice={dateInvoice}
                                                        onGenerate={this.handleGenerateGeneral}
                                                        onUpdateData={this.handleUpdateDataToGenerate} onUpdateDateElement={this.handleUpdateDateElement} />

        return <>
            <div className="default-page default-page-col-2">
                <div className="col-1">
                    <div className="contracts-process-menu">
                        <div className="item item-generate">
                            {generateAll}
                        </div>
                        <div className="item">
                            <div>Dernière génération globale le {society.dateContractStringSlash}</div>
                        </div>
                        {society.dateInvoiceStringSlash && <>
                            <div className="item">
                                <div>Dernière facturation le {society.dateInvoiceStringSlash}</div>
                            </div>
                        </>}
                        <div className="item">
                            <div className="years">
                                <Years year={year} yearToday={yearToday} monthToday={monthToday} />
                            </div>
                            <div className="months">
                                <Months year={year} month={month} yearToday={yearToday} monthToday={monthToday} />
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-2">
                    <div>
                        <div className="toolbar">
                            <div className="item">
                                <h2>{MONTHS[month - 1]} {year}</h2>
                            </div>
                        </div>

                        <div className="toolbar">
                            <div className="item filter-search">
                                <Search onSearch={onSearch} placeholder="Recherche par nom"/>
                            </div>
                        </div>

                        <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                             currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                        <div className="items-table">
                            <div className="items items-default">
                                <div className="item item-header">
                                    <div className="item-content">
                                        <div className="item-body">
                                            <div className="infos infos-col-3">
                                                <div className="col-1">Contrat</div>
                                                <div className="col-2">Périodicité</div>
                                                <div className="col-3 actions">Actions</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {data && data.length !== 0 ? data.map(elem => {
                                    return <ContractsItemProcess {...this.props} elem={elem} key={elem.id}
                                                                 year={year} month={month}
                                                                 enableItemAction={enableItemAction}
                                                                 onGenerate={this.handleGenerate} />
                                }) : <Alert>Aucun résultat</Alert>}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Aside ref={this.asideGenerate} content={contentGenerate}>Modifier la date de facturation</Aside>
            <Aside ref={this.asideGenerateGeneral} content={contentGenerateGeneral} classes="generate-general">Dates de facturation à modifier</Aside>
        </>
    }
}

function Years ({ year, yearToday, monthToday }) {
    let yearPrev = year - 1;
    let yearNext = year + 1;
    let monthPrev = yearPrev === yearToday ? monthToday : 12;
    let monthNext = yearNext === yearToday ? monthToday : 1;

    return <div className="days">
        <a className="day" href={Routing.generate(URL_INDEX, {'year': yearPrev, month: monthPrev})}>
            <span className="icon-left-arrow"/>
        </a>
        <div className="day active">{year}</div>
        {yearNext > yearToday ? <>
            <a className="day disabled"><span className="icon-right-arrow"/></a>
        </> : <>
            <a className="day" href={Routing.generate(URL_INDEX, {'year': yearNext, month: monthNext})}>
                <span className="icon-right-arrow"/>
            </a>
        </>}
    </div>
}

function Months ({ year, month, yearToday, monthToday }) {

    return MONTHS.map((el, index) => {

        let index1 = index + 1;

        return <a href={Routing.generate(URL_INDEX, {'year': year, month: index1})} key={index}
                  className={(index1 === month ? "active " : "") + (isInferiorOf(year, index1, yearToday, monthToday) ? "disabled" : "")} >
            <span>
                <span>{Sanitaze.addZeroToNumber(index1)}</span>
                <span>-</span>
                <span>{el}</span>
            </span>
        </a>
    })
}

function getDateValues(date) {
    date.setHours(0,0,0);

    let yearToday = date.getFullYear();
    let monthToday = date.getMonth() + 1;

    return [yearToday, monthToday]
}

function isInferiorOf(year0, month0, year1, month1) {
    if(year0 < year1 || (year0 === year1 && month0 < month1)){
        return true;
    }

    return false;
}