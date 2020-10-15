import React, {Component} from "react";
import {BarChart, LineChart, Bar, Line, XAxis, YAxis, Legend, CartesianGrid, Tooltip, ResponsiveContainer} from 'recharts';


class Charts extends Component{

    constructor(props){
        super(props);
    }

    getRandomColor() {
        let max = 1 << 24;
        return '#' + (max + Math.floor(Math.random()*max)).toString(16).slice(-6);
    }

    render() {
        const monthNames = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        let data = []
        let salesPerMonth = this.props.data
        if (salesPerMonth.length != 0) {
            Object.keys(salesPerMonth).forEach((salePerMonthKey) => {
                let itemObject = {}
                let copySalePerMonthKey = salePerMonthKey
                
                itemObject['name'] = monthNames[+copySalePerMonthKey.split('-')[0] - 1]
                salesPerMonth[salePerMonthKey].forEach((salePerProduct) => {
                    let productName = salePerProduct.name
                    itemObject[productName] = salePerProduct.totalSale
                })
    
                data.push(itemObject)
            })
        }

        

        let barList = this.props.products.map((product) => 
            <Bar key={product.id} dataKey={product.name} stackId="a" fill={this.getRandomColor()} />
        )

        return (
            <ResponsiveContainer height='100%' width='100%' aspect={4.0/3.0}>
            <BarChart data={data}
                margin={{top: 5, right: 30, left: 20, bottom: 5}}>
                <XAxis dataKey="name"/>
                <YAxis/>
                <CartesianGrid strokeDasharray="3 3"/>
                <Tooltip/>
                <Legend />

                {barList}
            </BarChart>
            </ResponsiveContainer>
        )
    }
}

export default Charts;