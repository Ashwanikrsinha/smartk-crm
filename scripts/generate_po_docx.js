#!/usr/bin/env node
/**
 * SmartK PO Document Generator
 * Generates the exact PO format the client specified.
 * Called by Laravel: node generate_po_docx.js <json_data_file> <output_path>
 *
 * Usage:
 *   node generate_po_docx.js /tmp/po_data_12345.json /tmp/po_output_12345.docx
 */

const fs = require('fs');
const path = require('path');

const {
    Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
    AlignmentType, BorderStyle, WidthType, ShadingType,
    HeadingLevel, UnderlineType, NumberFormat
} = require('docx');

// ── Load data from JSON file passed as argument ──────────
const dataFile = process.argv[2];
const outputFile = process.argv[3];

if (!dataFile || !outputFile) {
    console.error('Usage: node generate_po_docx.js <data.json> <output.docx>');
    process.exit(1);
}

const data = JSON.parse(fs.readFileSync(dataFile, 'utf8'));

/**
 * data shape:
 * {
 *   po_number:        "PO-2025-0001",
 *   po_date:          "15 April, 2025",
 *   school_name:      "ABC Public School",
 *   school_address:   "123 MG Road",
 *   school_city:      "Mumbai",
 *   school_state:     "Maharashtra",
 *   contact_name:     "Mrs. Sunita Sharma",
 *   contact_designation: "Principal",
 *   contact_phone:    "9876543210",
 *   contact_email:    "principal@abc.edu",
 *   items: [
 *     { name: "Level 1 Teacher Kit", rate: "5000", qty: 10, total: 50000 },
 *     { name: "Level 2 Teacher Kit", rate: "6000", qty:  5, total: 30000 }
 *   ],
 *   grand_total:      80000,
 *   pdcs: [
 *     { label: "PDC 1", cheque_number: "123456", amount: 40000, date: "20 April, 2025" }
 *   ],
 *   remarks: ""
 * }
 */

// ── Helpers ───────────────────────────────────────────────

const TNR = "Times New Roman";

function tnr(text, opts = {}) {
    return new TextRun({
        text,
        font: TNR,
        size: opts.size ?? 24,           // 12pt default
        bold: opts.bold ?? false,
        underline: opts.underline ? { type: UnderlineType.SINGLE } : undefined,
        shading: opts.highlight ? { type: ShadingType.CLEAR, color: "auto", fill: "FFFF00" } : undefined,
    });
}

function para(children, opts = {}) {
    return new Paragraph({
        alignment: opts.align ?? AlignmentType.BOTH,
        spacing: opts.spacing ?? undefined,
        indent: opts.indent ?? undefined,
        numbering: opts.numbering ?? undefined,
        children: Array.isArray(children) ? children : [children],
    });
}

function emptyPara() {
    return para([tnr("")]);
}

function cell(children, width, opts = {}) {
    const border = { style: BorderStyle.SINGLE, size: 6, color: "000000" };
    const borders = { top: border, bottom: border, left: border, right: border };
    return new TableCell({
        width: { size: width, type: WidthType.DXA },
        borders,
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        shading: opts.shading ? { fill: opts.shading, type: ShadingType.CLEAR } : undefined,
        children: Array.isArray(children) ? children : [
            para(Array.isArray(children) ? children : [tnr(children, opts)], { align: AlignmentType.BOTH })
        ],
    });
}

// ── T&C lines (fixed) ─────────────────────────────────────

const TC_LINES = [
    "S. Chand Edutech Pvt. Ltd. would retain the License to the product given and it is not to be copied, modified, translated, decompiled, or otherwise used in any other manner than for teaching children in the school.",
    "The Educational Institute can use the program only at its location set out in the Order Form.",
    "In the event of education institute failing to make the payment to Edutech mutually agreed on in the Purchase order, Edutech remedies include terminating this PO without notice and recalling the material already delivered under the PO. If the outstanding amount is not paid, even after the mutually agreed T&C, interest of 2% per month is paid in full.",
    "Edutech shall not be held responsible or liable for not performing any of its obligations or undertakings provided for in this form if such performance is prevented, delayed or hindered by an act of god, fire, flood, explosion, riots, inability to procure labour, equipments, facilities, supplies, failure of transportation, strikes, lock outs not within the reasonable control of Edutech.",
    "All payments shall be non-refundable once made.",
    "Once order is received, number of kits ordered cannot be reduced.",
    "All the outstanding payments must be cleared within 3 Months of order delivery.",
    "In case of any dishonour of a PDC, S. Chand Edutech shall immediately bring the matter to the knowledge of the school.",
    "The school shall take immediate steps to ensure that the reason for dishonour is removed & intimate S. Chand Edutech to represent the cheque again within 2 working days or remit the amount via RTGS/NEFT.",
    "Cheque dishonouring charges shall be borne by the school.",
    "Any missing material needs to be notified by the school within 15 working days after receiving the material, after that if the school notify us then the school will pay the delivery charges as well the missing material charges.",
];

// ── Build document sections ───────────────────────────────

const children = [];

// ── [1] Header ────────────────────────────────────────────
children.push(para(
    [tnr("To Be Printed on School Letter Head", { bold: true, underline: true })],
    { align: AlignmentType.CENTER }
));
children.push(emptyPara());

// ── [2] Date & PO Number ──────────────────────────────────
children.push(para([tnr(`Date: ${data.po_date}`)]));
children.push(para([tnr(`Purchase Order No: ${data.po_number}`)]));

// ── [3] To block ──────────────────────────────────────────
children.push(para([tnr("To,")], { spacing: { after: 0 } }));
children.push(para([tnr("S. Chand Edutech Pvt. Ltd.")]));
children.push(para([tnr("A-27, Block B, Mohan Cooperative Industrial Estate,")]));
children.push(para([tnr("Badarpur, New Delhi, Delhi 110044")]));

// ── [4] Subject ───────────────────────────────────────────
children.push(para([tnr("Sub:  Purchase Order for SmartK- An NCERT Based Preschool Curriculum")]));

// ── [5] Salutation ────────────────────────────────────────
children.push(para([tnr("Dear Ma\u2019am,")]));

// ── [6] Opening body ──────────────────────────────────────
children.push(para([
    tnr("We are pleased to inform you that the school management of "),
    tnr(`${data.school_name} is`, { bold: true }),
    tnr(" ready to deploy SmartK curriculum in the school."),
]));
children.push(emptyPara());

// ── [7] Numbered section 1: Product deployment ────────────
children.push(para(
    [tnr("The product will be deployed in the following manner:", { bold: true })],
    {
        numbering: { reference: "main-list", level: 0 },
        align: AlignmentType.BOTH,
    }
));

// ── [8] Products table ────────────────────────────────────
// Column widths (DXA): 3609 | 993 | 2101 | 1958 = 8661 total
const COL = [3609, 993, 2101, 1958];

const headerRow = new TableRow({
    children: [
        cell([para([tnr("Product Purchased")])], COL[0]),
        cell([para([tnr("Selling Price")])], COL[1]),
        cell([para([tnr("Number of Kits Ordered")])], COL[2]),
        cell([para([tnr("Total Amount Payable")])], COL[3]),
    ],
});

const itemRows = data.items.map(item => new TableRow({
    children: [
        cell([para([tnr(item.name)])], COL[0]),
        cell([para([tnr(String(item.rate), { bold: true })])], COL[1]),
        cell([para([tnr(String(item.qty), { bold: true })])], COL[2]),
        cell([para([tnr(String(item.total), { bold: true })])], COL[3]),
    ],
}));

const totalRow = new TableRow({
    height: { value: 488, rule: "atLeast" },
    children: [
        cell([para([tnr("Total Amount Payable")])], COL[0]),
        cell([para([tnr("")])], COL[1]),
        cell([para([tnr("")])], COL[2]),
        cell([para([tnr(String(data.grand_total), { bold: true })], { align: AlignmentType.BOTH })], COL[3]),
    ],
});

children.push(new Table({
    width: { size: 8661, type: WidthType.DXA },
    columnWidths: COL,
    rows: [headerRow, ...itemRows, totalRow],
}));

children.push(emptyPara());

// ── [9] Numbered section 2: Payment & Delivery Terms ──────
children.push(para(
    [tnr("Payment & Delivery Terms:", { bold: true })],
    { numbering: { reference: "main-list", level: 0 }, align: AlignmentType.BOTH }
));

children.push(para(
    [tnr("Delivery Terms", { bold: true })],
    { indent: { left: 360 }, align: AlignmentType.BOTH }
));

children.push(para(
    [tnr("Delivery Time: Within 20 days from receiving the order.")],
    { indent: { left: 360 }, align: AlignmentType.BOTH }
));

children.push(para(
    [tnr("Total Amount Payable")],
    { indent: { left: 360 }, align: AlignmentType.BOTH }
));

// PDC entries (dynamic)
data.pdcs.forEach(pdc => {
    children.push(para(
        [tnr(`Amount Received before delivery of kits: ${pdc.label} - Cheque No. ${pdc.cheque_number} - ₹${pdc.amount}`, { highlight: true })],
        { indent: { left: 360 }, align: AlignmentType.BOTH }
    ));
    children.push(para(
        [tnr(`dated: ${pdc.date}`, { highlight: true })],
        { indent: { left: 360 }, align: AlignmentType.BOTH }
    ));
});

children.push(para(
    [tnr("Delivery of the boxes is subjected to the clearance of the advance cheque")],
    { indent: { left: 360 }, align: AlignmentType.BOTH }
));

// ── [10] Numbered section 3: SPOC & Delivery details ──────
children.push(para(
    [tnr("SPOC & Details of the school where the delivery needs to be done:", { bold: true })],
    { numbering: { reference: "main-list", level: 0 }, align: AlignmentType.BOTH }
));

[
    `School/Org. Name: ${data.school_name}`,
    `Name: ${data.contact_name}`,
    `Designation: ${data.contact_designation}`,
    `Contact Number: ${data.contact_phone}`,
    `Email: ${data.contact_email}`,
    `Communication Address: ${data.school_address}`,
    `City: ${data.school_city}                                                   State: ${data.school_state}`,
].forEach(line => {
    children.push(para([tnr(line)], { indent: { left: 360 }, align: AlignmentType.BOTH }));
});

// Spacing before T&C
for (let i = 0; i < 3; i++) children.push(emptyPara());

// ── [11] Numbered section 4: Terms & Conditions ───────────
children.push(para(
    [tnr("Terms & Conditions", { bold: true })],
    { numbering: { reference: "main-list", level: 0 }, align: AlignmentType.BOTH }
));
children.push(emptyPara());

TC_LINES.forEach(line => {
    children.push(para(
        [tnr(line, { bold: true })],
        {
            numbering: { reference: "tc-list", level: 0 },
            spacing: { line: 240, lineRule: "auto" },
            align: AlignmentType.BOTH,
        }
    ));
});

// ── [12] Remarks ──────────────────────────────────────────
children.push(emptyPara());
children.push(para([
    tnr("Special requirement / Remarks -  ", { highlight: true }),
    tnr(data.remarks
        ? data.remarks
        : "_______________________________________________________"),
], { spacing: { line: 240, lineRule: "auto" }, align: AlignmentType.BOTH }));

// ── [13] School sign-off ──────────────────────────────────
children.push(emptyPara());
children.push(para([tnr(`For and on behalf of : ${data.school_name}`)], { align: AlignmentType.BOTH }));
children.push(para([tnr("Name:")], { align: AlignmentType.BOTH }));
children.push(para([tnr("Designation:")], { align: AlignmentType.BOTH }));
children.push(para([tnr("Signature:____________________________")], { align: AlignmentType.BOTH }));

// ── Build Document ────────────────────────────────────────
const doc = new Document({
    numbering: {
        config: [
            {
                reference: "main-list",
                levels: [{
                    level: 0,
                    format: NumberFormat.DECIMAL,
                    text: "%1.",
                    alignment: AlignmentType.LEFT,
                    style: {
                        run: { font: TNR, size: 24, bold: true },
                        paragraph: { indent: { left: 360, hanging: 360 } },
                    },
                }],
            },
            {
                reference: "tc-list",
                levels: [{
                    level: 0,
                    format: NumberFormat.DECIMAL,
                    text: "%1.",
                    alignment: AlignmentType.LEFT,
                    style: {
                        run: { font: TNR, size: 24, bold: true },
                        paragraph: { indent: { left: 360, hanging: 360 }, spacing: { line: 240 } },
                    },
                }],
            },
        ],
    },
    sections: [{
        properties: {
            page: {
                // A4: 11906 x 16838 DXA (matching original)
                size: { width: 11906, height: 16838 },
                margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 },
            },
        },
        children,
    }],
});

Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outputFile, buffer);
    console.log(`Generated: ${outputFile}`);
    process.exit(0);
}).catch(err => {
    console.error('Error generating DOCX:', err);
    process.exit(1);
});
