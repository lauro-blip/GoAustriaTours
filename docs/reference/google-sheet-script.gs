/**
 * GoAustriaTours: logs every website form submission into this spreadsheet, one tab per form.
 * Setup: docs/reference/GOOGLE_SHEET.md
 *
 * 1. Replace the SECRET below with any long random text (the same text goes into Admin > Google Sheet).
 * 2. Deploy > New deployment > Web app > Execute as: Me, Who has access: Anyone.
 */
const SECRET = 'PASTE_A_LONG_RANDOM_SECRET_HERE';

function doPost(e) {
  const lock = LockService.getScriptLock();
  try {
    lock.waitLock(20000);
    const data = JSON.parse(e.postData.contents);
    if (!data || data.secret !== SECRET) return out({ ok: false, error: 'wrong secret' });
    if (!data.tab || !data.row) return out({ ok: false, error: 'missing tab or row' });

    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sh = ss.getSheetByName(data.tab) || ss.insertSheet(data.tab);

    // Header row: keep the existing columns, add any new ones on the right.
    const lastCol = sh.getLastColumn();
    const headers = lastCol ? sh.getRange(1, 1, 1, lastCol).getValues()[0].map(String) : [];
    Object.keys(data.row).forEach(function (k) { if (headers.indexOf(k) === -1) headers.push(k); });
    if (headers.length > lastCol) {
      if (sh.getMaxColumns() < headers.length) sh.insertColumnsAfter(sh.getMaxColumns(), headers.length - sh.getMaxColumns());
      const head = sh.getRange(1, 1, 1, headers.length);
      head.setNumberFormat('@');
      head.setValues([headers]);
      head.setFontWeight('bold');
      sh.setFrozenRows(1);
    }

    // Retries are safe: an Enquiry ID that is already in the tab is not added twice.
    const idCol = headers.indexOf('Enquiry ID') + 1;
    const id = data.row['Enquiry ID'];
    if (idCol && id && sh.getLastRow() > 1) {
      const ids = sh.getRange(2, idCol, sh.getLastRow() - 1, 1).getValues().map(function (r) { return String(r[0]); });
      if (ids.indexOf(String(id)) !== -1) return out({ ok: true, duplicate: true });
    }

    const values = headers.map(function (h) { return data.row[h] == null ? '' : String(data.row[h]); });
    const rowNum = sh.getLastRow() + 1;
    const range = sh.getRange(rowNum, 1, 1, values.length);
    range.setNumberFormat('@');   // plain text: nothing is turned into a formula or a date
    range.setValues([values]);
    return out({ ok: true, row: rowNum });
  } catch (err) {
    return out({ ok: false, error: String(err) });
  } finally {
    lock.releaseLock();
  }
}

function out(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}
