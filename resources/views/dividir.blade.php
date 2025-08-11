@extends('layout')

@section('main_content')
<div class="container-sm py-3">
    <h1 class="h4 text-center mb-3">Split the bill</h1>
    <p class="text-muted text-center mb-3">Enter a person's name on the left and their amount on the right. Use "+ Add row" to add more people.</p>

    <div class="input-group mb-2">
        <span class="input-group-text">Total</span>
        <input id="totalInput" type="text" inputmode="decimal" class="form-control text-end" placeholder="Total amount">
    </div>

    <div id="rows" class="d-grid gap-2"></div>

    <div class="d-flex gap-2 mt-2">
        <button id="addRowBtn" type="button" class="btn btn-success flex-fill">+ Add row</button>
        <button id="calcBtn" type="button" class="btn btn-primary flex-fill">Calculate</button>
    </div>

    <div id="results" class="mt-4 d-none">
        <h2 class="h5 mb-2">Results</h2>
        <div id="comparison" class="alert alert-info d-none" role="alert"></div>
        <ul id="resultsList" class="list-group"></ul>
    </div>
</div>

<script>
    (function () {
        const rowsContainer = document.getElementById('rows');
        const addRowBtn = document.getElementById('addRowBtn');
        const calcBtn = document.getElementById('calcBtn');
        const results = document.getElementById('results');
        const resultsList = document.getElementById('resultsList');
        const totalInput = document.getElementById('totalInput');
        const comparison = document.getElementById('comparison');

        function createRow() {
            const row = document.createElement('div');
            row.className = 'input-group';

            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.className = 'form-control';
            nameInput.placeholder = 'Name';
            nameInput.autocomplete = 'off';

            const amountInput = document.createElement('input');
            amountInput.type = 'text';
            amountInput.inputMode = 'decimal';
            amountInput.className = 'form-control text-end';
            amountInput.placeholder = 'Amount';
            amountInput.autocomplete = 'off';

            const removeBtnWrap = document.createElement('span');
            removeBtnWrap.className = 'input-group-text p-0 border-0 bg-transparent';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-outline-danger';
            removeBtn.textContent = '×';
            removeBtn.title = 'Remove row';
            removeBtn.addEventListener('click', () => {
                row.remove();
            });

            removeBtnWrap.appendChild(removeBtn);

            row.appendChild(nameInput);
            row.appendChild(amountInput);
            row.appendChild(removeBtnWrap);
            return row;
        }

        function addRow() {
            rowsContainer.appendChild(createRow());
        }

        function parseAmount(value) {
            if (!value) return 0;
            const normalized = value.replace(/\s+/g, '').replace(',', '.');
            const num = Number(normalized);
            return isFinite(num) ? num : 0;
        }

        function calculate() {
            const sumsByName = new Map();
            const rows = rowsContainer.querySelectorAll('.input-group');
            let sumOfPeople = 0;
            rows.forEach((row) => {
                const inputs = row.querySelectorAll('input');
                const name = (inputs[0]?.value || '').trim();
                const amount = parseAmount(inputs[1]?.value || '');
                if (!name || !amount) return;
                const prev = sumsByName.get(name) || 0;
                sumsByName.set(name, prev + amount);
                sumOfPeople += amount;
            });

            resultsList.innerHTML = '';

            // Comparison with total
            const totalEntered = parseAmount(totalInput.value || '');
            if (totalEntered > 0) {
                const diff = totalEntered - sumOfPeople;
                const absDiff = Math.abs(diff);
                if (absDiff < 0.005) {
                    comparison.textContent = `OK: people sum matches total (${totalEntered.toFixed(2)}).`;
                    comparison.className = 'alert alert-success';
                    comparison.classList.remove('d-none');
                } else {
                    const direction = diff > 0 ? 'below' : 'above';
                    comparison.textContent = `Difference: ${absDiff.toFixed(2)} (${direction} the total). People sum: ${sumOfPeople.toFixed(2)}, Total: ${totalEntered.toFixed(2)}.`;
                    comparison.className = 'alert alert-warning';
                    comparison.classList.remove('d-none');
                }
            } else {
                comparison.classList.add('d-none');
            }

            if (sumsByName.size === 0) {
                results.classList.add('d-none');
                return;
            }

            const sorted = Array.from(sumsByName.entries()).sort((a, b) => a[0].localeCompare(b[0]));
            for (const [name, sum] of sorted) {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `<span>${name}</span><span class=\"fw-bold\">${sum.toFixed(2)}</span>`;
                resultsList.appendChild(li);
            }

            results.classList.remove('d-none');
        }

        addRowBtn.addEventListener('click', addRow);
        calcBtn.addEventListener('click', calculate);

        for (let i = 0; i < 5; i += 1) addRow();
    })();
</script>
@endsection


