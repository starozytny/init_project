import React from "react";

export function Alert(props){
    const { type, title, withIcon = true, content=null, children } = props;

    let icon, alert;
    switch (type){
        case "danger":
            alert = "danger";
            icon = "warning";
            break;
        case "warning":
            alert = "warning";
            icon = "warning";
            break;
        case "info":
            alert = "primary";
            icon = "information";
            break;
        default:
            alert = "default";
            icon = "question";
            break;
    }

    return <div className={`alert alert-${alert}`}>
        {withIcon && <span className={`icon-${icon}`} />}
        {content}
        {children && <p>
            {title && <span className="title">{title}</span>}
            {children}
        </p>}
    </div>
}