import React, { Component } from 'react';

import { PageError } from "./PageError";
import { Pagination } from "./Pagination";

export class Page extends Component {
    constructor(props) {
        super(props);

        this.pagination = React.createRef();
    }

    render () {
        const { haveLoadPageError, children,
                havePagination, perPage = "10", taille, data,
        } = this.props;

        return <>
            {haveLoadPageError && <PageError />}
            <div className="main-content">
                {children}
                <Pagination ref={this.pagination} havePagination={havePagination} perPage={perPage} taille={taille} items={data}
                            onUpdate={(items) => this.props.onUpdate(items)}/>
            </div>

        </>
    }
}