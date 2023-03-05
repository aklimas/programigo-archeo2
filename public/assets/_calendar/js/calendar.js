
const StopEventPropagation = (e)=> {
    if (!e) return;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation();
};
const Calendar = (id) => ({
    id: id,
    data: [],
    el: undefined,
    y: undefined,
    m: undefined,
    onDateClick(e) {
        StopEventPropagation(e);
        const el = e.srcElement;
        var date  = new Date(el.getAttribute('date'));

        console.log(date);
        year  = date.getFullYear();
        month = (date.getMonth() + 1).toString().padStart(2, "0");
        day   = date.getDate().toString().padStart(2, "0");

        //$('.modal').modal('hide');
        console.log('otworz kalendarz');
        $('#modalDate_' + year+'-'+month+'-'+day).modal('show');
        $(function() {
            console.log('otworz kalendarz 2');
           modal('detailsCalendar_' + year+'-'+month+'-'+day);
        });



    },
    onEventClick(e) {
        StopEventPropagation(e);
        const el = e.srcElement;
        var id  = el.getAttribute('data-id');

        //$('.modal').modal('hide');
        $('#calDetails_' + id).modal('show');

    },
    bindData(events) {
        this.data = events.sort((a,b) => {
            if ( a.time < b.time ) return -1;
            if ( a.time > b.time ) return 1;
            return 0;
        });
    },
    renderEvents() {
        if (!this.data || this.data.length<=0) return;
        const lis = this.el.querySelectorAll(`.${this.id} .days .inside`);
        let y = this.el.querySelector('.month-year .year').innerText;
        let m = lis[0].querySelector('.date').getAttribute('month');
        lis.forEach((li)=>{
            try {
                let d = li.innerText;
                let divEvents = li.querySelector('.events');
                li.onclick = this.onDateClick;
                this.data.forEach((ev)=>{
                    try {
                        /*<div  data-id="${ev.id}" time="${ev.time}" class="event ${ev.cls}" style="background-color: ${ev.clr};">${evTime.format('h:mma')} ${ev.desc}</div> */
                        let evTime = moment(ev.time);
                        if (evTime.year() == y && evTime.month() == m && evTime.date() == d) {
                            let frgEvent = document.createRange().createContextualFragment(`
                                
                                <div  data-id="${ev.id}" time="${ev.time}" class="event ${ev.cls}" style="background-color: ${ev.clr};">${ev.desc}</div>
                            `);
                            divEvents.appendChild(frgEvent);
                            let divEvent = divEvents.querySelector(`.event[time='${ev.time}']`);

                            console.log(divEvent);

                            divEvent.onclick = this.onEventClick;
                        }
                    } catch (err2) {
                        console.log(err2);
                    }
                });
            } catch (err1) {
                console.log(err1);
            }
        });
    },
    render(y, m) {
        //-------------------------------------------------------------------------------------------
        //first time when you call render() without params, it is going to default to current date.
        //this logic here is to make sure if you re-render by calling render() without any param again,
        //if the calendar is already looking at some other month, then it will get the updated data, but
        //the calendar will not jump back to current month and stay at the previous month you are looking at.
        //this is useful when server side has updated events, calendar can re-bindData() and re-render()
        //itself correctly to reflect any changes.
        try {
            if (isNaN(y) && isNaN(this.y)) {
                this.y = moment().year();
            } else if ((!isNaN(y) && isNaN(this.y)) || (!isNaN(y) && !isNaN(this.y))) {
                this.y = y>1600 ? y : moment().year(); //calendar doesn't exist before 1600! :)
            }
            if (isNaN(m) && isNaN(this.m)) {
                this.m = moment().month();
            } else if ((!isNaN(m) && isNaN(this.m)) || (!isNaN(m) && !isNaN(this.m))) {
                this.m = m>=0 ? m : moment().month(); //momentjs month starts from 0-11
            }
            //------------------------------------------------------------------------------------------

            const d = moment().year(this.y).month(this.m).date(1); //first date of month
            const now = moment();
            const frgCal = document.createRange().createContextualFragment(`
            <div class="calendar noselect ">
                <div class="month-year-btn d-flex justify-content-center align-items-center mb-2">
                    <a class="prev-month btn btn-sm btn-primary mx-1"><i class="ri-arrow-left-fill fa-lg m-3"></i></a>
                    <div class="month-year d-flex justify-content-center align-items-center">
                        <div class="month mb-2 me-2">${moment().locale('pl').month(this.m).format('MMMM')} </div>
                        <div class="year mb-2 ml-1">${this.y}</div>
                    </div>
                    <a class="next-month  btn btn-sm btn-primary mx-1"><i class="ri-arrow-right-fill fa-lg m-3" aria-hidden="true"></i></a>
                </div>
                <ol class="day-names list-unstyled">
                    
                    <li><h6 class="initials">Pn</h6></li>
                    <li><h6 class="initials">Wt</h6></li>
                    <li><h6 class="initials">Śr</h6></li>
                    <li><h6 class="initials">Cz</h6></li>
                    <li><h6 class="initials">Pt</h6></li>
                    <li><h6 class="initials">Sb</h6></li>
                    <li><h6 class="initials">Nd</h6></li>
                </ol>
            </div>
            `);
            const frgCal2 = document.createRange().createContextualFragment(`
            <div class="calendar noselect ">
                <div class="month-year-btn d-flex justify-content-center align-items-center mb-2">
                    <a class="prev-month btn btn-primary mx-5"><i class="ri-arrow-left-fill fa-lg m-3"></i></a>
                    <div class="month-year d-flex justify-content-center align-items-center">
                        <div class="month mb-2 mr-2">${moment().lang('pl').month(this.m).format('MMMM')}</div>
                        <div class="year mb-2">${this.y}</div>
                    </div>
                    <a class="next-month  btn btn-primary mx-5"><i class="ri-arrow-right-fill fa-lg m-3" aria-hidden="true"></i></a>
                </div>
                <ol class="day-names list-unstyled">
                    
                    <li><h6 class="initials">Pn</h6></li>
                    <li><h6 class="initials">Wt</h6></li>
                    <li><h6 class="initials">Śr</h6></li>
                    <li><h6 class="initials">Cz</h6></li>
                    <li><h6 class="initials">Pt</h6></li>
                    <li><h6 class="initials">Sb</h6></li>
                    <li><h6 class="initials">Nd</h6></li>
                </ol>
            </div>
            `);
            const isSameDate = (d1, d2) => d1.format('YYYY-MM-DD') == d2.format('YYYY-MM-DD');
            let frgWeek;
            d.day(0); //move date to the oldest Sunday, so that it lines up with the calendar layout
            for(let i=0; i<5; i++){ //loop thru 35 boxes on the calendar month
                frgWeek = document.createRange().createContextualFragment(`
                <ol class="days list-unstyled" week="${d.week()}">
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100 position-absolute" >${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute"  >${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute"  >${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute"  >${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute">${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute">${d.format('D')}</div><div class="events mt-4"></div></li>
                    <li class="${d.add(1,'d'),this.m != d.month()?' outside':'inside'}${isSameDate(d,now)?' today':''} position-relative" data-id="${d}"><div date="${d}" month="${d.month()}" class="date h-100 w-100  position-absolute" >${d.format('D')}</div><div class="events mt-4"></div></li>
                </ol>
                `);
                frgCal.querySelector('.calendar').appendChild(frgWeek);
            }

            frgCal.querySelector('.prev-month').onclick = ()=>{
                const dp = moment().year(this.y).month(this.m).date(1).subtract(1, 'month');
                this.render(dp.year(), dp.month());
            };

            frgCal.querySelector('.next-month').onclick = ()=>{
                const dn = moment().year(this.y).month(this.m).date(1).add(1, 'month');
                this.render(dn.year(), dn.month());
            };
            this.el = document.getElementById(this.id);
            this.el.innerHTML = ''; //replacing
            this.el.appendChild(frgCal);
            this.renderEvents();
        } catch (error) {
            console.error(error);
        }
    }
});
const Spinner = (id) =>({
    id: id,
    el: null,
    renderSpinner() {
        const frgSpinner = document.createRange().createContextualFragment(`
        <div class="spinner d-flex justify-content-center align-items-center">
            <div class="spinner-grow text-light" style="width: 4rem; height: 4rem;" role="status">
                <span class="sr-only">Ładowanie...</span>
            </div>
        </div>
        `);
        this.el = document.getElementById(this.id);
        this.el.innerHTML = ''; //replacing
        this.el.appendChild(frgSpinner);
        return this;
    },
    async delay (delay = 2000) {
        await new Promise(resolve => setTimeout(resolve, delay));
    }
});

const ready = callback => {
    if (document.readyState !== 'loading') callback();
    else if (document.addEventListener)
        document.addEventListener('DOMContentLoaded', callback);
    else
        document.attachEvent('onreadystatechange', function () {
            if (document.readyState === 'complete') callback();
        });
};

ready(async () => {
    const cal = Calendar('calendar');
    const spr = Spinner('calendar');
    await spr.renderSpinner().delay(0);
    cal.bindData(mockData);
    cal.render();
});
