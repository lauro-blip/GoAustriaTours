// Phone fields: choosing a country puts its dial code in the field, and the number is checked as the visitor types
// (same rules as App\Support\PhoneQuality + the server's libphonenumber check, which has the final say).

const dialOf = (select) => {
    const m = /\+\d+/.exec(select.selectedOptions[0]?.textContent || '');
    return m ? m[0] : '';
};

const isPlaceholder = (national, dial) => {
    const d = national.replace(/\D/g, '');
    if (d.length < 6) return true;
    if (/^(\d)\1+$/.test(d)) return true;
    if ('01234567890123456789'.includes(d) || '98765432109876543210'.includes(d)) return true;
    if (d.length >= 8 && /^(\d{2,4})\1+\d{0,3}$/.test(d) && new Set(d).size <= 4) return true;
    if (dial === '+1' && d.length === 10) {
        const exchange = d.slice(3, 6);
        const line = d.slice(6);
        if (exchange === '555' && (line.startsWith('01') || line === '1212')) return true;
    }
    return false;
};

/** Returns an error message, or '' when the number looks fine. */
export function phoneProblem(dial, value) {
    const typed = value.trim();
    const digits = typed.replace(/\D/g, '');
    const dialDigits = dial.replace(/\D/g, '');
    if (!typed.startsWith(dial) || digits.length <= dialDigits.length) return 'Please enter your phone number.';
    let national = digits.slice(dialDigits.length);
    if (dial !== '+1') national = national.replace(/^0/, '');   // trunk prefix typed by habit, e.g. 07… in the UK
    if (dial === '+1') {
        if (national.length !== 10) return 'US and Canadian numbers have 10 digits after +1.';
        if (!/^[2-9]/.test(national) || !/^[2-9]/.test(national.slice(3))) return 'That area code or number is not valid.';
    } else if (national.length < 6 || national.length > 13) {
        return 'Please enter a valid phone number for the selected country.';
    }
    if (isPlaceholder(national, dial)) return 'That looks like a placeholder number. Please enter your real phone number.';
    return '';
}

function setup(select, input) {
    let prev = dialOf(select);

    const withDial = (value, dial) => {
        const rest = value.replace(/^\+\d+\s*/, '').replace(/^0+(?=\d)/, (z) => (dial === '+1' ? '' : z));
        return `${dial} ${rest.trimStart()}`;
    };
    const check = () => input.setCustomValidity(phoneProblem(dialOf(select), input.value));

    // Number pasted or autofilled with another country's code: switch the country to match.
    const adoptCountry = () => {
        const m = /^\+(\d{1,3})/.exec(input.value.trim());
        if (!m || input.value.trim().startsWith(dialOf(select))) return;
        const opt = [...select.options].find((o) => new RegExp(`\\+${m[1]}(\\D|$)`).test(o.textContent));
        if (opt) { select.value = opt.value; prev = dialOf(select); }
    };

    select.addEventListener('change', () => {
        const dial = dialOf(select);
        const old = prev.replace(/\D/g, '');
        let value = input.value.trim();
        if (value.startsWith(prev)) value = value.slice(prev.length);
        else if (value.startsWith('+' + old)) value = value.slice(old.length + 1);
        input.value = `${dial} ${value.trimStart()}`;
        prev = dial;
        check();
    });
    input.addEventListener('input', () => {
        adoptCountry();
        const dial = dialOf(select);
        if (!input.value.startsWith(dial)) input.value = withDial(input.value, dial);   // the code stays in the field
        check();
    });
    input.addEventListener('blur', () => { if (input.value.trim() === dialOf(select)) input.value = `${dialOf(select)} `; check(); });
    input.addEventListener('focus', () => {
        if (!input.value.trim()) input.value = `${dialOf(select)} `;
        requestAnimationFrame(() => input.setSelectionRange(input.value.length, input.value.length));
    });

    if (!input.value.trim()) input.value = `${dialOf(select)} `;
    else if (!/^\+/.test(input.value.trim())) input.value = withDial(input.value, dialOf(select));
    check();
}

export function initPhoneFields() {
    document.querySelectorAll('input[data-phone]').forEach((input) => {
        const select = input.closest('form')?.querySelector('select[name=country]');
        if (select) setup(select, input);
    });
}
