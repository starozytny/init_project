import React, { Component } from 'react';

function manageDoubleAside(isDoubleAside, isOpen){
    if(isDoubleAside) {
        let firstAside = document.querySelector(".main-content > .aside > .aside-content");
        let secondOverlay = document.querySelector(".main-content > .aside > .aside-content .aside > .aside-overlay");
        if (firstAside) {
            firstAside.scrollTop = 0;
            firstAside.style.overflow = isOpen ? "hidden" : "auto";
            if(secondOverlay){
                secondOverlay.style.backgroundColor = "rgba(24,24,24,0.7)"
            }
        }
    }
}

export class Aside extends Component {
    constructor (props) {
        super(props)

        this.state = {
            title: null,
            active: false,
            isDoubleAside: props.isDoubleAside ? props.isDoubleAside : false
        }

        this.handleOpen = this.handleOpen.bind(this)
        this.handleClose = this.handleClose.bind(this)
    }

    handleOpen = (title) => {
        manageDoubleAside(this.state.isDoubleAside, true);
        let body = document.querySelector("body");
        if(body){
            body.style.overflow = "hidden";
        }
        this.setState({ active: true, title: title })
    }
    handleClose = () => {
        manageDoubleAside(this.state.isDoubleAside, false);

        let body = document.querySelector("body");
        if(body){
            body.style.overflow = "auto";
        }
        this.setState({ active: false })
    }

    render () {
        const { content, children, classes } = this.props
        const { active, title } = this.state

        return <div className={"aside " + active + (classes ? " " + classes : "") }>
            <div className="aside-overlay" onClick={this.handleClose} />
            <div className="aside-content">
                <div className="aside-title">
                    <span className="title">{title ? title : children}</span>
                    <span className="icon-cancel" onClick={this.handleClose} />
                </div>
                {content}
            </div>
        </div>
    }
}
