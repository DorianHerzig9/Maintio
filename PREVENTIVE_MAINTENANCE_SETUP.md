# Preventive Maintenance Setup Guide

## Overview
This document explains how to set up and use the Preventive Maintenance system in Maintio.

## Database Setup

1. **Run the migration** to create the preventive maintenance table:
   ```bash
   php spark migrate
   ```

## Features

### Core Functionality
- **Flexible Scheduling**: Time-based intervals (daily, weekly, monthly, quarterly, annually) and usage-based intervals (hours, cycles, kilometers)
- **Automatic Work Order Generation**: System automatically creates work orders when maintenance is due
- **Priority Management**: Set priorities for maintenance tasks (low, medium, high, critical)
- **Asset Integration**: Link maintenance schedules to specific assets
- **User Assignment**: Assign default users to maintenance tasks
- **Dashboard Integration**: View upcoming and overdue maintenance on the main dashboard

### Scheduling Types
- **Time-based**: Daily, Weekly, Monthly, Quarterly, Annually
- **Usage-based**: Hours, Cycles, Kilometers (requires integration with usage tracking)

## Automatic Work Order Generation

### Manual Generation
Generate work orders immediately via the web interface:
1. Go to Preventive Maintenance page
2. Click "Arbeitsaufträge generieren" button
3. System will create work orders for all due maintenance

### Automatic Generation via Command Line
Use the CLI command for automated scheduling:

```bash
# Generate work orders for preventive maintenance
php spark maintenance:generate-work-orders

# Preview what would be generated (dry run)
php spark maintenance:generate-work-orders --dry-run

# Check maintenance due in next 14 days
php spark maintenance:generate-work-orders --days=14
```

### Cron Job Setup
For automatic generation, set up a cron job to run the command regularly:

**Linux/macOS:**
```bash
# Add this to your crontab (run daily at 6 AM)
0 6 * * * cd /path/to/your/project && php spark maintenance:generate-work-orders

# Run every 4 hours
0 */4 * * * cd /path/to/your/project && php spark maintenance:generate-work-orders
```

**Windows Task Scheduler:**
1. Open Task Scheduler
2. Create Basic Task
3. Set trigger (e.g., daily at 6:00 AM)
4. Action: Start a program
5. Program: `php`
6. Arguments: `spark maintenance:generate-work-orders`
7. Start in: `C:\path\to\your\project`

## Usage Guide

### Creating a Preventive Maintenance Schedule

1. **Navigate** to Preventive Maintenance → New Schedule
2. **Fill out the form**:
   - **Basic Info**: Name, Asset, Description
   - **Scheduling**: Interval type and value, Priority
   - **Details**: Task instructions, required tools, parts, safety notes
   - **Settings**: Auto-generation, lead time, default assignee

3. **Schedule Examples**:
   - Monthly filter cleaning: Interval=Monthly, Value=1
   - Quarterly inspection: Interval=Quarterly, Value=1  
   - Every 500 hours service: Interval=Hours, Value=500

### Managing Schedules

- **View All**: See all active schedules with due dates and status
- **Filter**: View overdue or upcoming maintenance
- **Search**: Find schedules by name, asset, or category
- **Edit**: Modify schedule parameters
- **Complete**: Mark maintenance as completed (calculates next due date)

### Dashboard Integration

The main dashboard shows:
- **Overdue Maintenance**: Red alerts for past-due maintenance
- **Upcoming Maintenance**: Maintenance due in next 14 days
- **Quick Actions**: Generate work orders and create new schedules

## Best Practices

### Scheduling
- **Set appropriate lead times**: 7-14 days for complex maintenance
- **Use categories**: Group similar maintenance (electrical, mechanical, safety)
- **Include detailed instructions**: Step-by-step procedures
- **Specify tools and parts**: Help technicians prepare

### Work Order Generation
- **Run generation daily**: Ensures timely work order creation
- **Monitor logs**: Check command output for errors
- **Review generated orders**: Verify accuracy and completeness

### Maintenance Completion
- **Mark completed promptly**: Keeps schedule accurate
- **Record actual completion date**: Helps with scheduling optimization
- **Update instructions**: Improve procedures based on experience

## Troubleshooting

### Common Issues

**Work orders not generating automatically:**
- Check cron job is running
- Verify lead time settings
- Ensure auto-generation is enabled on schedules

**Incorrect due dates:**
- Verify interval type and value
- Check timezone settings
- Review last completion date

**Missing preventive maintenance data:**
- Run migration: `php spark migrate`
- Check database table exists: `preventive_maintenance`
- Verify model is included in controllers

### Database Schema
The preventive maintenance table includes:
- Schedule information (name, description, intervals)
- Asset relationships
- Timing data (next due, last completed)
- Generation settings (auto-generate, lead time)
- Task details (tools, parts, safety notes)

## API Endpoints

The system provides API endpoints for integration:
- `GET /api/preventive-maintenance/overdue` - Get overdue maintenance
- `GET /api/preventive-maintenance/upcoming/{days}` - Get upcoming maintenance
- `GET /api/preventive-maintenance/stats` - Get maintenance statistics
- `POST /preventive-maintenance/generate-work-orders` - Generate work orders

## Integration Notes

- **Asset Management**: Schedules are linked to assets via foreign key
- **Work Orders**: Generated work orders follow standard work order workflow
- **User Management**: Default assignments respect user permissions
- **Dashboard**: Real-time integration with main dashboard widgets