import React, { Component } from 'react';

import axios             from "axios";
import toastr            from "toastr";
import Swal              from "sweetalert2";
import SwalOptions       from "@commonComponents/functions/swalOptions";
import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { UserList }       from "./UserList";
import { UserRead }       from "./UserRead";
import { UserFormulaire } from "./UserForm";

const URL_DELETE_ELEMENT    = 'api_users_delete';
const URL_DELETE_GROUP      = 'api_users_delete_group';
const MSG_DELETE_ELEMENT    = 'Supprimer cet utilisateur ?';
const MSG_DELETE_GROUP      = 'Aucun utilisateur sélectionné.';
let SORTER = Sort.compareLastname;

let sorters = [
    { value: 0, label: 'Nom', identifiant: 'sorter-nom' },
    { value: 1, label: 'Identifiant', identifiant: 'sorter-identifiant' },
    { value: 2, label: 'Email', identifiant: 'sorter-email' },
]

let sortersFunction = [Sort.compareLastname, Sort.compareUsername, Sort.compareEmail];

function searchFunction(dataImmuable, search){
    let newData = [];
    search = search.toLowerCase();
    newData = dataImmuable.filter(function(v) {
        if(v.username.toLowerCase().startsWith(search)
            || v.email.toLowerCase().startsWith(search)
            || v.firstname.toLowerCase().startsWith(search)
            || v.lastname.toLowerCase().startsWith(search)
        ){
            return v;
        }
    })

    return newData;
}

function filterFunction(dataImmuable, filters){
    let newData = [];
    if(filters.length === 0) {
        newData = dataImmuable
    }else{
        dataImmuable.forEach(el => {
            filters.forEach(filter => {
                if(filter === el.highRoleCode){
                    newData.filter(elem => elem.id !== el.id)
                    newData.push(el);
                }
            })
        })
    }

    return newData;
}

export class User extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sessionName: "user.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteGroup = this.handleDeleteGroup.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleGetFilters = this.handleGetFilters.bind(this);
        this.handleRegenPassword = this.handleRegenPassword.bind(this);
        this.handlePerPage = this.handlePerPage.bind(this);
        this.handleChangeCurrentPage = this.handleChangeCurrentPage.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees, SORTER, "read", "username"); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, SORTER); }

    handleDelete = (element) => {
        this.layout.current.handleDelete(this, element, Routing.generate(URL_DELETE_ELEMENT, {'id': element.id}), MSG_DELETE_ELEMENT);
    }

    handleDeleteGroup = () => {
        this.layout.current.handleDeleteGroup(this, Routing.generate(URL_DELETE_GROUP), MSG_DELETE_GROUP);
    }

    handleGetFilters = (filters) => { this.layout.current.handleGetFilters(filters, filterFunction); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, searchFunction, true, filterFunction); }

    handleRegenPassword = (elem) => {
        Swal.fire(SwalOptions.options("Réinitialiser son mot de passe", "Le nouveau mot de passe ne s'affichera <u>qu'une seule fois</u>. Pensez donc à le noter."))
            .then((result) => {
                if (result.isConfirmed) {
                    axios.post(Routing.generate('api_users_password_reinit', {'token': elem.token}), {})
                        .then(function (response) {
                            Swal.fire(response.data.message, '', 'success');
                            toastr.info("Mot de passe réinitialisé avec succès !");
                        })
                        .catch(function (error) {
                            Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
                        })
                    ;
                }
            })
        ;
    }

    handlePerPage = (perPage) => {
        this.layout.current.handleUpdatePerPage(SORTER, perPage);
        this.setState({ perPage: perPage });
    }

    handleChangeCurrentPage = (currentPage) => { this.setState({ currentPage }); }

    handleSorter = (nb) => {
        const { perPage } = this.state;

        SORTER = sortersFunction[nb];
        this.layout.current.handleUpdatePerPage(SORTER, perPage);
    }

    handleContentList = (currentData, changeContext, getFilters, filters, data) => {
        const { perPage, currentPage } = this.state;

        return <UserList onChangeContext={changeContext}
                         onDelete={this.handleDelete}
                         onDeleteAll={this.handleDeleteGroup}
                         developer={parseInt(this.props.developer)}
                         //filter-search
                         onSearch={this.handleSearch}
                         filters={filters}
                         onGetFilters={this.handleGetFilters}
                         //changeNumberPerPage
                         perPage={perPage}
                         onPerPage={this.handlePerPage}
                         //twice pagination
                         currentPage={currentPage}
                         onPaginationClick={this.layout.current.handleGetPaginationClick(this)}
                         taille={data.length}
                         //sorter
                         sorters={sorters}
                         onSorter={this.handleSorter}
                         //data
                         data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <UserFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        return <UserFormulaire type="update" element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentRead = (changeContext, element) => {
        return <UserRead elem={element} onChangeContext={changeContext} onRegenPassword={this.handleRegenPassword}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} search={this.props.search} onGetData={this.handleGetData}
                    onContentList={this.handleContentList} onContentRead={this.handleContentRead}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}
                    onChangeCurrentPage={this.handleChangeCurrentPage}/>
        </>
    }
}