function filter(dataImmuable, filters, property) {
    let newData = [];
    if(filters.length === 0) {
        newData = dataImmuable
    }else{
        dataImmuable.forEach(el => {
            filters.forEach(filter => {
                let push = false;
                if(property === "isNatif"){
                    if(filter === 0 && el.isNatif === false){
                        push = true;
                    }

                    if(filter === 1 && el.isNatif === true){
                        push = true;
                    }
                }else{
                    if(filter === el[property]){
                        push = true;
                    }
                }

                if(push){
                    newData.filter(elem => elem.id !== el.id)
                    newData.push(el);
                }
            })
        })
    }

    return newData;
}

function filterHighRoleCode(dataImmuable, filters){
    return filter(dataImmuable, filters, "highRoleCode");
}

function filterStatus(dataImmuable, filters){
    return filter(dataImmuable, filters, "status");
}

function filterType(dataImmuable, filters){
    return filter(dataImmuable, filters, "type");
}

function filterNatif(dataImmuable, filters){
    return filter(dataImmuable, filters, "isNatif");
}

module.exports = {
    filter,
    filterHighRoleCode,
    filterStatus,
    filterType,
    filterNatif,
}
