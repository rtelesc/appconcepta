import React, { Component } from 'react';
import axios from 'axios';

class TicketListItemPhoto extends Component {
	render() {
		return(
			<li>
				<img src={this.props.data.Url} alt={this.props.data.Description} />
			</li>
		)
	}
}

class TicketListItem extends Component {
	render() {		
		return(
			<div className="ticket-list__item">								
				<div className="item__code">Code: {this.props.code}</div>
				<div className="item__destination">Destination: {this.props.data.Destination}</div>
				<div className="item__name">Name: {this.props.data.Name}</div>
				<div className="item__photos">
					<ul>
					{
						this.props.data.Photos.map(photo => <TicketListItemPhoto data={photo} />)
					}
					</ul>
				</div>
			</div>
		)
	}
}

export default class TicketList extends Component {
	constructor(props) {
		super(props);

		this.state = {	
			code: '',
			tickets: []
		};
	}

	componentWillMount() {
		axios.get('http://localhost/Portfolio/wp-concepta-test/wp-json/ticket-list/v1/test')
			.then(res => {				
				const tickets = res.data.result;				
				this.setState({ code: res.data.code, tickets });
			})
	}

	render() {	
		return(
			<div className="ticket-list">				
				{
					this.state.tickets.map(ticket => <TicketListItem code={this.state.code} data={ticket} />)
				}				
			</div>
		)
	}
}