import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { UserForm }      from "./UserForm";
import { Button }        from "@dashboardComponents/Tools/Button";

export class UserUpdate extends Component {
    render () {
        const { onChangeContext, onUpdateList, element } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item">
                        <Button icon="left-arrow" type="default" onClick={() => onChangeContext("list")}>Retour à la liste</Button>
                    </div>
                </div>
                <div className="form">
                    <h2>Modifier {element.username}</h2>
                    <UserForm
                        context="update"
                        url={Routing.generate('api_users_update', {'id': element.id})}
                        username={element.username}
                        firstname={element.firstname}
                        lastname={element.lastname}
                        email={element.email}
                        roles={element.roles}
                        onUpdateList={onUpdateList}
                        messageSuccess="Félicitation ! La mise à jour s'est réalisé avec succès !"
                    />
                </div>
            </div>
        </>
    }
}