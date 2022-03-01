function filter(dataImmuable, filters, property) {
    let newData = [];
    if(filters.length === 0) {
        newData = dataImmuable
    }else{
        dataImmuable.forEach(el => {
            filters.forEach(filter => {
                if(filter === el[property]){
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

module.exports = {
    filter,
    filterHighRoleCode,
    filterStatus,
    filterType,
}