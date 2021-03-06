import React, { Component } from 'react';

export class Menu extends Component {
    constructor(props) {
        super();

        this.state = {
            isOpened: false
        }

        this.handleOpenMenu = this.handleOpenMenu.bind();
    }

    componentDidMount () {
        const { menu } = this.props

        let tab = location.pathname.split("/");
        tab = tab.filter(elem => elem !== "");

        tab.forEach(element => {
            JSON.parse(menu).forEach(el => {
                if(element === el.name){
                    this.setState({active: element})
                }
            })

        });
    }

    handleOpenMenu = () => {
        this.setState(prevState => {
            return { isOpened: !prevState.isOpened }
        })
    }

    render () {
        const { menu, menuBottom } = this.props
        const { isOpened, active } = this.state

        return <>
            <div className="nav-mobile" onClick={this.handleOpenMenu}>
                <span className={`icon-${ isOpened ? "cancel" : "menu" }`} />
            </div>
            <div className={`nav-body ${isOpened}`}>
                <div className="items">
                    { <MenuItem menu={menu} active={active} /> }
                </div>
                <div className="items">
                    { <MenuItem menu={menuBottom} active={active}/> }
                </div>
            </div>
        </>
    }
}

function MenuItem (props){
    const { menu, active } = props

    return (
        JSON.parse(menu).map(el => {
            return <div key={el.name} className="item">
                <a href={el.path} className={ active === el.name ? "active" : "" }>
                    <span className={`icon-${el.icon}`} />
                    <span>{el.label}</span>
                </a>
            </div>
        })
    )
}