@extends('layout')

@section('main_content')
<div class="container-sm py-3">
    <h1 class="h4 text-center mb-3">Split the bill</h1>
    <p class="text-muted text-center mb-2">First add people's names, then add prices and select who shares each price.</p>
    
    <div id="globalError" class="alert alert-danger d-none" role="alert">Please fix invalid amounts. Only numbers are allowed. Use a dot for decimals, for example 15.95.</div>

    <!-- People Management Section -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">People</h5>
        </div>
        <div class="card-body">
            <div id="peopleList" class="d-grid gap-2"></div>
            <button id="addPersonBtn" type="button" class="btn btn-outline-primary mt-2">+ Add Person</button>
        </div>
    </div>

    <!-- Prices Section -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Prices</h5>
        </div>
        <div class="card-body">
            <div id="pricesList" class="d-grid gap-2"></div>
            <button id="addPriceBtn" type="button" class="btn btn-outline-success mt-2">+ Add Price</button>
        </div>
    </div>

    <!-- Total Section -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Total</h5>
        </div>
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text">Total Amount</span>
                <input id="totalInput" type="text" inputmode="decimal" class="form-control text-end" placeholder="Total amount">
            </div>
        </div>
    </div>

    <div class="d-grid">
        <button id="calcBtn" type="button" class="btn btn-primary btn-lg">Calculate</button>
    </div>

    <div id="results" class="mt-4 d-none">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Results</h5>
            </div>
            <div class="card-body">
                <div id="comparison" class="alert alert-info d-none" role="alert"></div>
                <ul id="resultsList" class="list-group list-group-flush"></ul>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const peopleList = document.getElementById('peopleList');
        const addPersonBtn = document.getElementById('addPersonBtn');
        const pricesList = document.getElementById('pricesList');
        const addPriceBtn = document.getElementById('addPriceBtn');
        const calcBtn = document.getElementById('calcBtn');
        const results = document.getElementById('results');
        const resultsList = document.getElementById('resultsList');
        const totalInput = document.getElementById('totalInput');
        const comparison = document.getElementById('comparison');
        const globalError = document.getElementById('globalError');

        let people = [];
        let prices = [];

        function createPersonRow() {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group';

            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.className = 'form-control';
            nameInput.placeholder = 'Person name';
            nameInput.autocomplete = 'off';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-outline-danger';
            removeBtn.textContent = '×';
            removeBtn.title = 'Remove person';

            removeBtn.addEventListener('click', () => {
                wrapper.remove();
                updatePeopleArray();
                updatePriceDropdowns();
            });

            nameInput.addEventListener('input', updatePeopleArray);

            wrapper.appendChild(nameInput);
            wrapper.appendChild(removeBtn);
            return wrapper;
        }

        function createPriceRow() {
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3';

            const row1 = document.createElement('div');
            row1.className = 'input-group mb-2';

            const amountInput = document.createElement('input');
            amountInput.type = 'text';
            amountInput.inputMode = 'decimal';
            amountInput.className = 'form-control text-end';
            amountInput.placeholder = 'Price amount';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-outline-danger';
            removeBtn.textContent = '×';
            removeBtn.title = 'Remove price';

            row1.appendChild(amountInput);
            row1.appendChild(removeBtn);

            const row2 = document.createElement('div');
            row2.className = 'd-flex gap-2 align-items-center mb-2';

            const splitCheckbox = document.createElement('input');
            splitCheckbox.type = 'checkbox';
            splitCheckbox.className = 'form-check-input';
            splitCheckbox.id = 'split_' + Date.now();

            const splitLabel = document.createElement('label');
            splitLabel.className = 'form-check-label me-2';
            splitLabel.htmlFor = splitCheckbox.id;
            splitLabel.textContent = 'Split equally among selected people';

            row2.appendChild(splitCheckbox);
            row2.appendChild(splitLabel);

            const row3 = document.createElement('div');
            row3.className = 'd-flex gap-2 align-items-center mb-2';

            const peopleLabel = document.createElement('span');
            peopleLabel.className = 'text-muted me-2';
            peopleLabel.textContent = 'People:';

            const peopleSelect = document.createElement('select');
            peopleSelect.className = 'form-select';
            peopleSelect.style.minWidth = '200px';

            // Add a placeholder option
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = 'Select a person...';
            peopleSelect.appendChild(placeholderOption);

            const addPersonBtn = document.createElement('button');
            addPersonBtn.type = 'button';
            addPersonBtn.className = 'btn btn-outline-secondary btn-sm';
            addPersonBtn.textContent = 'Add to price';

            row3.appendChild(peopleLabel);
            row3.appendChild(peopleSelect);
            row3.appendChild(addPersonBtn);

            const row4 = document.createElement('div');
            row4.className = 'd-flex gap-2 flex-wrap';
            row4.id = 'selectedPeople_' + Date.now();

            const selectedPeopleLabel = document.createElement('span');
            selectedPeopleLabel.className = 'text-muted me-2';
            selectedPeopleLabel.textContent = 'Selected:';
            row4.appendChild(selectedPeopleLabel);

            const selectedPeopleContainer = document.createElement('div');
            selectedPeopleContainer.className = 'd-flex gap-1 flex-wrap';
            row4.appendChild(selectedPeopleContainer);

            let selectedPeople = [];

            addPersonBtn.addEventListener('click', () => {
                const selectedValue = peopleSelect.value;
                if (selectedValue && !selectedPeople.includes(selectedValue)) {
                    selectedPeople.push(selectedValue);
                    updateSelectedPeopleDisplay();
                    peopleSelect.value = '';
                }
            });

            function updateSelectedPeopleDisplay() {
                selectedPeopleContainer.innerHTML = '';
                selectedPeople.forEach(person => {
                    const personTag = document.createElement('span');
                    personTag.className = 'badge bg-primary me-1 d-flex align-items-center gap-1';
                    personTag.innerHTML = 
                        ${person}
                        <button type="button" class="btn-close btn-close-white" 
                                style="font-size: 0.5rem;" 
                                onclick="removePerson('${person}', this)"></button>
                    ;
                    selectedPeopleContainer.appendChild(personTag);
                });
            }

            // Global function to remove person (needs to be accessible)
            window.removePerson = function(personName, button) {
                const index = selectedPeople.indexOf(personName);
                if (index > -1) {
                    selectedPeople.splice(index, 1);
                    updateSelectedPeopleDisplay();
                }
            };

            row3.appendChild(peopleLabel);
            row3.appendChild(peopleSelect);
            row3.appendChild(addPersonBtn);

            // Validation for amount input
            const allowedPattern = /^[0-9]*\.?[0-9]*$/;
            function setInvalid(isInvalid) {
                if (isInvalid) {
                    amountInput.classList.add('is-invalid');
                } else {
                    amountInput.classList.remove('is-invalid');
                }
            }

            function validateCurrentValue() {
                const val = amountInput.value.trim();
                if (val === '') {
                    setInvalid(false);
                    return true;
                }
                const isValid = allowedPattern.test(val);
                setInvalid(!isValid);
                return isValid;
            }

            amountInput.addEventListener('input', validateCurrentValue);
            amountInput.addEventListener('keydown', (e) => {
                const controlKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Tab'];
                if (controlKeys.includes(e.key) || (e.ctrlKey || e.metaKey)) return;
                if (e.key === '.') {
                    if (amountInput.value.includes('.')) {
                        e.preventDefault();
                    }
                    return;
                }
                if (e.key >= '0' && e.key <= '9') return;
                e.preventDefault();
            });

            amountInput.addEventListener('paste', (e) => {
                const text = (e.clipboardData || window.clipboardData).getData('text');
                if (!allowedPattern.test(text)) {
                    e.preventDefault();
                    setInvalid(true);
                }
            });

            removeBtn.addEventListener('click', () => {
                wrapper.remove();
                updatePricesArray();
            });

            wrapper.appendChild(row1);
            wrapper.appendChild(row2);
            wrapper.appendChild(row3);
            wrapper.appendChild(row4);

            // Store selectedPeople in the wrapper for later access
            wrapper.selectedPeople = selectedPeople;
            return wrapper;
        }

        function updatePeopleArray() {
            people = [];
            peopleList.querySelectorAll('input').forEach(input => {
                const name = input.value.trim();
                if (name) {
                    people.push(name);
                }
            });
            updatePriceDropdowns();
        }

        function updatePricesArray() {
            prices = [];
            pricesList.querySelectorAll('.border').forEach(priceWrapper => {
                const amountInput = priceWrapper.querySelector('input[placeholder="Price amount"]');
                const splitCheckbox = priceWrapper.querySelector('input[type="checkbox"]');
                const selectedPeople = priceWrapper.selectedPeople || [];

                const amount = parseAmount(amountInput.value);

                if (amount > 0 && selectedPeople.length > 0) {
                    prices.push({
                        amount,
                        isSplit: splitCheckbox.checked,
                        selectedPeople
                    });
                }
            });
        }

        function updatePriceDropdowns() {
            pricesList.querySelectorAll('select').forEach(select => {
                // Keep the placeholder option
                const placeholderOption = select.querySelector('option[value=""]');
                select.innerHTML = '';
                if (placeholderOption) {
                    select.appendChild(placeholderOption);
                }
                
                people.forEach(person => {
                    const option = document.createElement('option');
                    option.value = person;
                    option.textContent = person;
                    select.appendChild(option);
                });
            });
        }

        function parseAmount(value) {
            if (!value) return 0;
            const normalized = value.replace(/\s+/g, '');
            const num = Number(normalized);
            return isFinite(num) ? num : 0;
        }

        function calculate() {
            globalError.classList.add('d-none');

            // Check for invalid amounts
            const invalidInput = pricesList.querySelector('.is-invalid');
            if (invalidInput) {
                globalError.classList.remove('d-none');
                results.classList.add('d-none');
                return;
            }

            updatePeopleArray();
            updatePricesArray();

            if (people.length === 0) {
                globalError.textContent = 'Please add at least one person.';
                globalError.classList.remove('d-none');
                results.classList.add('d-none');
                return;
            }

            if (prices.length === 0) {
                globalError.textContent = 'Please add at least one price.';
                globalError.classList.remove('d-none');
                results.classList.add('d-none');
                return;
            }

            // Calculate sums by person
            const sumsByName = new Map();
            people.forEach(person => sumsByName.set(person, 0));

            prices.forEach(price => {
                if (price.isSplit && price.selectedPeople.length > 0) {
                    const splitAmount = price.amount / price.selectedPeople.length;
                    price.selectedPeople.forEach(person => {
                        const current = sumsByName.get(person) || 0;
                        sumsByName.set(person, current + splitAmount);
                    });
                } else {
                    // If not split, add to first person
                    if (price.selectedPeople.length > 0) {
                        const current = sumsByName.get(price.selectedPeople[0]) || 0;
                        sumsByName.set(price.selectedPeople[0], current + price.amount);
                    }
                }
            });

            // Show results
            resultsList.innerHTML = '';
            const sorted = Array.from(sumsByName.entries()).sort((a, b) => a[0].localeCompare(b[0]));
            
            for (const [name, sum] of sorted) {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = <span>${name}</span><span class="fw-bold">${sum.toFixed(2)}</span>;
                resultsList.appendChild(li);
            }

            // Comparison with total
            const totalEntered = parseAmount(totalInput.value || '');
            if (totalEntered > 0) {
                const sumOfPeople = Array.from(sumsByName.values()).reduce((a, b) => a + b, 0);
                const diff = totalEntered - sumOfPeople;
                const absDiff = Math.abs(diff);
                
                if (absDiff < 0.005) {
                    comparison.textContent = OK: people sum matches total (${totalEntered.toFixed(2)}).;
                    comparison.className = 'alert alert-success';
                    comparison.classList.remove('d-none');
                } else {
                    const direction = diff > 0 ? 'below' : 'above';
                    comparison.textContent = Difference: ${absDiff.toFixed(2)} (${direction} the total). People sum: ${sumOfPeople.toFixed(2)}, Total: ${totalEntered.toFixed(2)}.;
                    comparison.className = 'alert alert-warning';
                    comparison.classList.remove('d-none');
                }
            } else {
                comparison.classList.add('d-none');
            }

            results.classList.remove('d-none');
        }

        addPersonBtn.addEventListener('click', () => {
            peopleList.appendChild(createPersonRow());
        });

        addPriceBtn.addEventListener('click', () => {
            pricesList.appendChild(createPriceRow());
            updatePriceDropdowns();
        });

        calcBtn.addEventListener('click', calculate);

        // Initialize with 3 people and 2 prices
        for (let i = 0; i < 3; i++) {
            peopleList.appendChild(createPersonRow());
        }
        for (let i = 0; i < 2; i++) {
            pricesList.appendChild(createPriceRow());
        }
        updatePriceDropdowns();
    })();
</script>
@endsection