"use strict";
function updateField(fieldNameAttribute,value){
    if(value !== null){
        let ipField = document?.querySelector(`input[name='${fieldNameAttribute}']`);
        if(ipField?.value !== undefined){
            ipField.value=value;
        }
    }
}
let params = (new URL(document.location)).searchParams;
let domain = params.get('domain');
let ip = params.get('ip');
updateField('url', domain);
updateField('ip',ip);