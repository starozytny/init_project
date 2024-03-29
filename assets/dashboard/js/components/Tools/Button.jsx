import React from "react";

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

export function ButtonIcon(props){
    const { icon, isSubmit=false, children, text, onClick, element="button", target="_self", tooltipWidth=null } = props;

    let divStyle = tooltipWidth ? { width: tooltipWidth + "px" } : null;

    if(element === "button"){
        return <button className="btn-icon" type={isSubmit ? "submit" : "button"} onClick={onClick}>
            <span className={`icon-${icon}`} />
            {text && <span>{text}</span>}
            {children && <span className="tooltip" style={divStyle}>{children}</span>}
        </button>
    }else{
        return <a className="btn-icon" target={target} href={onClick}>
            <span className={`icon-${icon}`} />
            {text && <span>{text}</span>}
            {children && <span className="tooltip" style={divStyle}>{children}</span>}
        </a>
    }
}

export function Button(props){
    const { icon, type="primary", isSubmit=false, outline=false, children, onClick, element="button", target="_self" } = props;

    if(element === "button"){
        return <button className={`btn btn-${outline ? "outline-" : ""}${type}`} type={isSubmit ? "submit" : "button"} onClick={onClick}>
            {icon && <span className={`icon-${icon}`} />}
            <span>{children}</span>
        </button>
    }else{
        return <a className={`btn btn-${outline ? "outline-" : ""}${type}`} target={target} href={onClick}>
            {icon && <span className={`icon-${icon}`} />}
            <span>{children}</span>
        </a>
    }
}

export function ButtonDropdown(props){
    const { items, children } = props;

    return <div className="btn-dropdown">
        <Button {...props}>{children}</Button>
        <div className="dropdown-items">
            {items.map((item, index) => {
                if(item) {
                    return <div className="item" key={index}>
                        {item.data}
                    </div>
                }
            })}
        </div>
    </div>
}

export function ButtonIconDropdown(props){
    const { items, children } = props;

    return <div className="btn-dropdown">
        <ButtonIcon {...props}>{children}</ButtonIcon>
        <div className="dropdown-items">
            {items.map((item, index) => {
                if(item){
                    return <div className="item" key={index}>
                        {item.data}
                    </div>
                }
            })}
        </div>
    </div>
}

export function LinkContact({ isClient=true, email }){
    return <a href={Routing.generate(isClient ? "user_mails_send" : "admin_mails_send", {'dest': [email]})}>
        Envoyer un mail
    </a>
}

export function ButtonIconContact({ isClient=true, email }){
    return <ButtonIcon icon="chat-2" onClick={Routing.generate(isClient ? "user_mails_send" : "admin_mails_send", {'dest': [email]})} element="a">
        Contacter
    </ButtonIcon>
}

export function ButtonIconContacts({ isClient=true, emails }){
    return <ButtonIcon icon="chat-2" onClick={Routing.generate(isClient ? "user_mails_send" : "admin_mails_send", {'dest': emails})} element="a">
        Contacter
    </ButtonIcon>
}