export const SET_TRANSFERS = ((state, data) => {
    state.transfers = data;
});

export const ADD_TRANSFERS = ((state, data) => {
    const ids = _.map(state.transfers, 'id');
    const arr = [];
    _.forEach(data, (item) => {
        if (_.includes(ids, item.id)) {
            const index = _.findIndex(state.transfers, { id: item.id });
            if (index !== -1) {
                state.transfers.splice(index, item);
            }
        } else {
            arr.push(item);
        }
    });
    state.transfers.push(...arr);
});

export const UPDATE_TRANSFERS = ((state, data) => {
    const arr = [];
    _.forEach(data, (item) => {
        const index = _.findIndex(state.transfers, { id: item.id });
        if (index !== -1) {
            state.transfers.splice(index, 1, item);
        } else {
            arr.push(item);
        }
    });
    state.transfers.unshift(...arr);
});

export const ADD_TRANSFER = ((state, elem) => {
    const index = _.findIndex(state.transfers, { id: elem.id });
    if (index === -1) {
        state.transfers.unshift(elem);
    }
});

export const UPDATE_TRANSFER = ((state, elem) => {
    const index = _.findIndex(state.transfers, { id: elem.id });
    if (index !== -1) {
        state.transfers.splice(index, 1, elem);
    }
});

export const UPDATE_TRANSFER_CLIENT = ((state, [elem]) => {
    const index = _.findIndex(state.transfersClient, { id: elem.id });
    state.transfersClient.splice(index, 1, elem);
});

export const ADD_TRANSFER_CLIENT = ((state, [elem]) => {
    state.transfersClient.unshift(elem);
});

export const SET_TRANSFERS_CLIENT = ((state, data) => {
    state.transfersClient = data;
});
