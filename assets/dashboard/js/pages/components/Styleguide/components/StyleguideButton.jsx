import React from "react";

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";

export function StyleguideButton () {
    return (
        <section>
            <h2>Boutons</h2>
            <div className="buttons-items">

                <Button type="default">Default</Button>
                <Button>Primary</Button>
                <Button type="danger">Danger</Button>
                <Button type="warning">Warning</Button>
                <Button type="default" icon="padlock">Default</Button>

                <ButtonIcon icon="pencil">Modifier</ButtonIcon>
                <ButtonIcon icon="pencil" text="Default" />

            </div>
        </section>
    )
}