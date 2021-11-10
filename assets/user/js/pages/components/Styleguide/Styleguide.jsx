import React, { Component } from "react";

import { StyleguideForm }      from "./components/StyleguideForm";
import { StyleguideMaps }      from "./components/StyleguideMaps";
import { StyleguideLozad }     from "./components/StyleguideLozad";
import { StyleguideTable }     from "./components/StyleguideTable";
import { StyleguideAside }     from "./components/StyleguideAside";
import { StyleguideAlert }     from "./components/StyleguideAlert";
import { StyleguideButton }    from "./components/StyleguideButton";

export class Styleguide extends Component {
    render () {
        return <div className="main-content">
            <StyleguideAlert />
            <StyleguideButton />
            <StyleguideForm />
            {/*<StyleguideTable />*/}
            {/*<StyleguideMaps />*/}
            {/*<StyleguideAside />*/}
            {/*<StyleguideLozad />*/}
        </div>
    }
}