import React, {Component} from "react";
import { ResponsiveContainer,Tooltip, PieChart, Pie, Sector, Cell, Legend} from 'recharts';


class PieCharts extends Component{

    constructor(props){
        super(props);
    }

    getRandomColor() {
        let max = 1 << 24;
        return '#' + (max + Math.floor(Math.random()*max)).toString(16).slice(-6);
    }

    render() {



        let data = this.props.data;

        const COLORS = ['#4073c4', '#ed8840',"#d61717","#300101","#137de7","#fc0dab","#f5fc0d","#22a610"];
        const RADIAN = Math.PI / 180;    

        const formatter = (value) => `${new Intl.NumberFormat('en').format(value)}`;
        const renderCustomizedLabel = ({ cx, cy, midAngle, innerRadius, outerRadius, percent, index }) => {
          const RADIAN = Math.PI / 180;
          // eslint-disable-next-line
          const radius = 25 + innerRadius + (outerRadius - innerRadius);
          // eslint-disable-next-line
          const x = cx + radius * Math.cos(-midAngle * RADIAN);
          // eslint-disable-next-line
          const y = cy + radius * Math.sin(-midAngle * RADIAN);

          return (
            <text
              x={x}
              y={y}
              fill="#8884d8"
              textAnchor={x > cx ? "start" : "end"}
              dominantBaseline="central"
              fontSize="9px"
            >
              {data[index].name} ({`${(percent * 100).toFixed(0)}%`})
            </text>
          );};


        
        return (
            <ResponsiveContainer height='100%' width='100%' aspect={3.0/2.0}>
                <PieChart width={800} height={400} onMouseEnter={this.onPieEnter}>
                        <Pie
                          data={data} 
                          dataKey="total"
                          cx={300} 
                          cy={200} 
                          labelLine={true}
                          label={renderCustomizedLabel}
                          outerRadius={170} 
                          fill="#8884d8"
                        >
                            {
                            data.map((entry, index) => <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]}/>)
                          }
                        </Pie>
                        
                        <Tooltip />
                      </PieChart>
            </ResponsiveContainer>
        )
    }
}

export default PieCharts;