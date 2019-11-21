export const SET_TRANSPORTERS = ((state, data) => {
    state.transporters = data;
});

export const SET_TRANSPORTER = ((state, data) => {
    state.transporter = data;
});

export const ADD_TRANSPORTER = ((state, transporter) => {
    state.transporters.push(transporter);
});

export const SET_TRANSPORTER_PRICE = ((state, data) => {
    state.transporterPrice = data;
});
