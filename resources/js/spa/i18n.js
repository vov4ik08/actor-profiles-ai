const messages = {
    app: {
        title: 'Actor Profile AI',
    },
    nav: {
        form: 'Form',
        actors: 'Actors',
    },
    form: {
        title: 'Submit actor info',
        help: 'Please enter your first name and last name, and also provide your address.',
        email: 'Email',
        emailPlaceholder: 'you@example.com',
        descriptionLabel: 'Actor description',
        descriptionPlaceholder: 'Describe the actor...',
        submit: 'Submit',
        submitting: 'Submitting…',
    },
    actors: {
        title: 'Actors',
        subtitle: 'All submitted actors.',
        refresh: 'Refresh',
        loading: 'Loading…',
        empty: 'No data yet.',
        unknown: '—',
        pagination: {
            rowsPerPage: 'Rows per page',
            previous: 'Previous',
            next: 'Next',
            pageOf: 'Page {current} of {last}',
            showing: 'Showing {from}–{to} of {total}',
        },
        columns: {
            firstName: 'First Name',
            lastName: 'Last Name',
            address: 'Address',
            height: 'Height',
            weight: 'Weight',
            gender: 'Gender',
            age: 'Age',
        },
        units: {
            cm: 'cm',
            kg: 'kg',
        },
    },
    errors: {
        failedToLoad: 'Failed to load data.',
        validation: 'Validation error.',
        submissionFailed: 'Submission failed.',
    },
};

function getByPath(obj, path) {
    return path.split('.').reduce((acc, key) => (acc && acc[key] != null ? acc[key] : undefined), obj);
}

function applyParams(str, params) {
    if (!params) return str;
    return str.replace(/\{(\w+)\}/g, (_, name) => (params[name] != null ? String(params[name]) : `{${name}}`));
}

export function t(key, params) {
    const value = getByPath(messages, key);
    return typeof value === 'string' ? applyParams(value, params) : key;
}

